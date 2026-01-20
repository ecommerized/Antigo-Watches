<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\OrderPlaced;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Traits\OrderPricing;
use App\Traits\WalletTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;

class POSController extends Controller
{
    use WalletTransaction, OrderPricing;
    public function __construct(
        private Branch      $branch,
        private Category    $category,
        private Order       $order,
        private OrderDetail $orderDetail,
        private Product     $product,
        private User        $user,
        Private Coupon      $coupon
    )
    {
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): Factory|View|Application
    {
        $category = $request->query('category_id', 0);
        $categories = $this->category->where(['position' => 0])->active()->get();
        $keyword = $request->keyword;
        $key = explode(' ', $keyword);
        $products = $this->product->where('total_stock', '>', 0)
            ->when($request->has('category_id') && $request['category_id'] != 0, function ($query) use ($request) {
                $query->whereJsonContains('category_ids', [['id' => (string)$request['category_id']]]);
            })
            ->when($keyword, function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->active()->latest()->paginate(Helpers::pagination_limit());

        $branch = $this->branch->find(auth('branch')->id());
        $users = $this->user->all();
        $coupons = $this->couponList(session()->get('customer_id'));
        return view('branch-views.pos.index', compact('categories', 'products', 'category', 'keyword', 'branch','users', 'coupons'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function quickView(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->product_id);
        if ($request->filled('product_list'))
        {
            $cart = collect($request->product_list ?? [])->filter(fn($value, $key) => is_array($value))->values();
        } else {
            $cart = collect(session()->get('cart', []))->filter(fn($value, $key) => is_array($value))->values();
        }
        $cartProduct = $cart->where('id', $request->product_id)->values();
        $variations = json_decode($product->variations, true) ?? [];
        $productVariation = collect($variations)->first()['type'] ?? '';
        $quantity = 1;
        $price = 0;
        $stock = !empty($variations) ? collect($variations)->first()['stock'] ?? 0 : $product->total_stock;
        $buttonText = translate('Add to Cart');

        if ($productVariation && is_array($variations)) {
            $matchedVariation = collect($variations)->firstWhere('type', $productVariation);
            if ($matchedVariation) {
                $matchedCart = $cartProduct->firstWhere('variant', $productVariation);
                $stock = $matchedVariation['stock'];
                if ($matchedCart) {
                    $quantity = $matchedCart['quantity'];
                    $price = ($matchedCart['price'] - Helpers::discount_calculate($product, $matchedCart['price'])) * $quantity;
                    $buttonText = translate('Update Cart');
                } else {
                    $price = $matchedVariation['price'] - Helpers::discount_calculate($product, $matchedVariation['price']);
                }
            }
        } elseif ($cartProduct->isNotEmpty()) {
            $quantity = $cartProduct[0]['quantity'];
            $price = ($cartProduct[0]['price'] - Helpers::discount_calculate($product, $cartProduct[0]['price'])) * $quantity;
            $buttonText = translate('Update Cart');
        } else {
            $price = $product->price - Helpers::discount_calculate($product, $product->price);
        }
        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos._quick-view-data', compact('product', 'quantity', 'price', 'stock', 'buttonText'))->render(),
        ]);
    }

    public function quickViewModalFooter(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->id);
        if ($request->filled('product_list'))
        {
            $cart = collect($request->product_list ?? [])->filter(fn($value, $key) => is_array($value))->values();
        } else {
            $cart = collect(session()->get('cart', []))->filter(fn($value, $key) => is_array($value))->values();
        }

        $cartProduct = $cart->where('id', $request->id)->values();
        $str = '';
        foreach (json_decode($product->choice_options) as $key => $choice) {
            $option = str_replace(' ', '', $request[$choice->name]);
            $str .= ($str !== '') ? '-' . $option : $option;
        }
        $quantity = 1;
        $price = 0;
        $stock = 0;
        $buttonText = translate('Add to Cart');
        $variations = json_decode($product->variations, true) ?? [];
        if (!empty($str) && is_array($variations)) {
            $matchedVariation = collect($variations)->firstWhere('type', $str);
            if ($matchedVariation) {
                $matchedCart = $cartProduct->firstWhere('variant', $str);
                $stock = $matchedVariation['stock'];
                if ($matchedCart) {
                    $quantity = $matchedCart['quantity'];
                    $price = ($matchedCart['price'] - Helpers::discount_calculate($product, $matchedCart['price'])) * $quantity;
                    $buttonText = translate('Update Cart');
                } else {
                    $price = $matchedVariation['price'] - Helpers::discount_calculate($product, $matchedVariation['price']);
                }
            }
        }

        return response()->json([
            'success' => 1,
            'stock' => $stock,
            'view' => view('branch-views.pos.partials.quick-view-modal-footer', compact('quantity', 'price', 'stock', 'buttonText'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return float[]|int[]
     */
    public function variantPrice(Request $request): array
    {
        $product = $this->product->find($request->id);
        $str = '';
        $price = 0;
        $stock = 0;

        foreach (json_decode($product->choice_options) as $key => $choice) {
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }

        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price - Helpers::discount_calculate($product, $product->price);
                    $stock = json_decode($product->variations)[$i]->stock;
                }
            }
        } else {
            $price = $product->price - Helpers::discount_calculate($product, $product->price);
            $stock = $product->total_stock;
        }

        return array('price' => ($price * $request->quantity), 'stock' => $stock);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomers(Request $request): JsonResponse
    {
        $key = explode(' ', $request['q']);
        $data = DB::table('users')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            })
            ->limit(8)
            ->latest()
            ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateTax(Request $request): RedirectResponse
    {
        if ($request->tax < 0) {
            Toastr::error(translate('Tax_can_not_be_less_than_0_percent'));
            return back();
        } elseif ($request->tax > 100) {
            Toastr::error(translate('Tax_can_not_be_more_than_100_percent'));
            return back();
        }

        $cart = $request->session()->get('cart', collect([]));
        $cart['tax'] = $request->tax;
        $request->session()->put('cart', $cart);
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateDiscount(Request $request): RedirectResponse|JsonResponse
    {
        $total = $request->session()->get('total');
        $cart = $request->session()->get('cart', collect([]));

        if ($cart->isEmpty()) {
            return $this->returnError($request, 'cart_is_empty');
        }

        if ($request->type == 'percent' && $request->discount < 0) {
            return $this->returnError($request, 'Extra_discount_can_not_be_less_than_0_percent');
        }

        if ($request->type == 'amount' && $request->discount < 0) {
            return $this->returnError($request, 'Extra_discount_can_not_be_less_than_0');
        }

        if ($request->type == 'percent' && $request->discount > 100) {
            return $this->returnError($request, 'Extra_discount_can_not_be_more_than_100_percent');
        }

        if ($request->type == 'amount' && $request->discount > $total) {
            return $this->returnError($request, 'Extra_discount_can_not_be_more_than_total_price');
        }

        if ($request->type == 'percent' && ($request->session()->get('cart')) == null) {
            return $this->returnError($request, 'cart_is_empty');
        }

        $cart['extra_discount'] = $request->discount;
        $cart['extra_discount_type'] = $request->type;
        $request->session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'success_message' => translate('Extra_discount_applied_successfully')]);
        }

        Toastr::success(translate('Discount_applied'));
        return back();
    }

    public function deleteDiscount(Request $request): RedirectResponse|JsonResponse
    {
        $cart = session()->get('cart', collect());
        if ($cart->isNotEmpty())
        {
            unset($cart['extra_discount'], $cart['extra_discount_type']);
        }

        if ($request->ajax())
        {
            return response()->json(['success' => true, 'success_message' => translate('Extra discount deleted successfully')]);
        }

        Toastr::success(translate('Extra discount deleted successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });

        $request->session()->put('cart', $cart);
        $this->calculatePOSCouponAndExtraDiscount();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addToCart(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $stock = 0;

        foreach (json_decode($product->choice_options) as $key => $choice) {
            $data[$choice->name] = $request[$choice->name];
            $variations[$choice->title] = $request[$choice->name];
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }
        $data['variations'] = $variations;
        $data['variant'] = $str;

        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price;
                    $stock = json_decode($product->variations)[$i]->stock;
                }
            }
        } else {
            $price = $product->price;
            $stock = $product->total_stock;
        }

        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = Helpers::discount_calculate($product, $price);
        $data['image'] = $product->image_fullpath;
        $data['total_stock'] = $stock;

        if ($request->filled('product_list'))
        {
            $cart = $request->product_list ?? [];
        } else {
            $cart = $request->session()->get('cart', []);
        }
        $cart = collect($cart);
        $cartItems = collect($cart)->filter(fn($value, $key) => is_array($value))->values();
        $existingProductKey = $cartItems->search(function ($item) use ($str, $product) {
            return $item['id'] == $product->id && $item['variant'] == $str;
        });

        if ($existingProductKey !== false) {
            $existingProduct = $cartItems->get($existingProductKey);
            $existingProduct['quantity'] = (int)$request['quantity'];

            if ($existingProduct['quantity'] > $existingProduct['total_stock']) {
                $existingProduct['quantity'] = $existingProduct['total_stock'];
            }

            $cart->put($existingProductKey, $existingProduct);
        } else {
            $cart->push($data);
        }
        if (!$request->filled('product_list')){
            $request->session()->put('cart', $cart);
        }
        $this->calculatePOSCouponAndExtraDiscount();
        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function cartItems(): Factory|View|Application
    {
        return view('branch-views.pos._cart');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function emptyCart(Request $request): JsonResponse
    {
        session()->forget('cart');
        Session::forget('customer_id');
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', []);
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
            if (count(collect($cart)->filter(fn($value, $key) => is_array($value))->values()) < 1) {
                session()->forget('cart');
            }

            $this->calculatePOSCouponAndExtraDiscount();
        }
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function orderList(Request $request): Factory|View|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $this->order->where('checked', 0)->update(['checked' => 1]);
        $query = $this->order->where(['branch_id' => auth('branch')->id()])->pos()->with(['customer', 'branch']);
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = Carbon::parse($endDate)->endOfDay();
            $query = $query->whereBetween('created_at', [$start, $end]);
        }
        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('order_status', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%");
            });
        }

        $queryParam = collect([
            'per_page' => $perPage,
            'search' => $search,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ])->filter(fn($value) => filled($value))->all();

        $orders = $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($queryParam);

        return view('branch-views.pos.order.list', compact('orders', 'search', 'startDate', 'endDate','perPage'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function orderDetails($id): View|Factory|RedirectResponse|Application
    {
        $order = $this->order->with('details')->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        if (isset($order)) {
            return view('branch-views.order.order-view', compact('order'));
        } else {
            Toastr::info('No more orders!');
            return back();
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function placeOrder(Request $request): RedirectResponse
    {
        $customer = $this->user->with('userAccount')->where('id', $request->session()->get('customer_id'))->first();
        $walletBalance = $customer?->userAccount?->wallet_balance ?? 0;
        $cart = $request->session()->get('cart', []);
        $cartItems = collect($cart)->filter(fn($value, $key) => is_array($value))->values();
        if ($cartItems->isEmpty()) {
            Toastr::error(translate('Cart is empty'));
            return back();
        }

        $totalTaxAmount = 0;
        $order_details = [];

        $order = $this->order->create([
            'user_id' => session()->has('customer_id') ? session('customer_id') : 0,
            'coupon_discount_title' => $request->coupon_discount_title == 0 ? null : 'coupon_discount_title',
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'order_type' => 'pos',
            'paid_amount' => $request->type == 'multiple' ? $request->paid_amount + $walletBalance : $request->paid_amount,
            'coupon_code' => $request->coupon_code ?? null,
            'payment_method' => $request->type,
            'additional_payment_method' => $request->type == 'multiple' ? ['wallet', $request->additional_payment_type] : [],
            'additional_payment_amount' => $request->type == 'multiple' ? ['wallet' => $walletBalance, $request->additional_payment_type => $request->paid_amount] : [],
            'transaction_reference' => $request->transaction_reference ?? null,
            'delivery_charge' => 0,
            'delivery_address_id' => $request->delivery_address_id ?? null,
            'order_note' => null,
            'checked' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'branch_id' =>  auth('branch')->id(),
        ]);

        $cartTotalProductPrice = 0;
        $cartTotalProductDiscountPrice = 0;
        foreach ($cartItems as $c) {
            if (is_array($c)) {
                $product = $this->product->find($c['id']);
                $p['variations'] = gettype($product['variations']) != 'array' ? json_decode($product['variations'], true) : $product['variations'];

                if (!empty($p['variations'])) {
                    $type = $c['variant'];
                    foreach ($p['variations'] as $var) {
                        if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                            Toastr::error($product->name . ' ' . $var['type'] . ' ' . translate('is out of stock'));
                            return back();
                        }
                    }
                } else {
                    if (($product->total_stock - $c['quantity']) < 0) {
                        Toastr::error($product->name . ' ' . translate('is out of stock'));
                        return back();
                    }
                }
                $price = $c['price'];
                $product = Helpers::product_data_formatting($product);
                $productSubtotal = ($c['price']) * $c['quantity'];
                $singleProductDiscount = Helpers::discount_calculate($product, $price);
                $discountOnProduct = ($singleProductDiscount * $c['quantity']);
                $singleProductTax = Helpers::tax_calculate($product, ($price - $singleProductDiscount));
                $taxOnProduct = ($singleProductTax * $c['quantity']);
                if ($product) {
                    $order_details[] = [
                        'product_id' => $c['id'],
                        'product_details' => $product,
                        'quantity' => $c['quantity'],
                        'price' => $price,
                        'tax_amount' => $singleProductTax,
                        'discount_on_product' => $singleProductDiscount,
                        'discount_type' => 'discount_on_product',
                        'variant' => empty($c['variant']) ? null : $c['variant'],
                        'variation' => json_encode($c['variations']),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];


                    $totalTaxAmount += $taxOnProduct;
                    $cartTotalProductPrice += $productSubtotal;
                    $cartTotalProductDiscountPrice += $discountOnProduct;
                }
                $var_store = [];
                if (!empty($product['variations'])) {
                    $type = $c['variant'];
                    foreach ($product['variations'] as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] -= $c['quantity'];
                        }
                        $var_store[] = $var;
                    }
                }

                $this->product->where(['id' => $product['id']])->update([
                    'variations' => json_encode($var_store),
                    'total_stock' => $product['total_stock'] - $c['quantity'],
                ]);

            }
        }

        $cartTotalProductPriceAfterProductDiscount = ($cartTotalProductPrice -$cartTotalProductDiscountPrice);

        $cartTotalAfterProductDiscountAndCouponDiscount = $cartTotalProductPriceAfterProductDiscount;

        if (isset($cart['coupon_discount']))
        {
            $couponDiscount = $cart['coupon_discount'];
            $cartTotalAfterProductDiscountAndCouponDiscount = ($cartTotalProductPriceAfterProductDiscount - $couponDiscount);
        }

        $cartTotalAfterProductDiscountAndCouponDiscountExtraDiscount = $cartTotalAfterProductDiscountAndCouponDiscount;
        if (isset($cart['extra_discount'])) {
            $extra_discount = $cart['extra_discount_type'] == 'percent' && $cart['extra_discount'] > 0
                ? (($cartTotalAfterProductDiscountAndCouponDiscount * $cart['extra_discount']) / 100)
                : $cart['extra_discount'];
            $cartTotalAfterProductDiscountAndCouponDiscountExtraDiscount =($cartTotalAfterProductDiscountAndCouponDiscount- $extra_discount) ;
        }

        try {
            DB::beginTransaction();
            $order->extra_discount = $extra_discount ?? 0;
            $order->total_tax_amount = $totalTaxAmount;
            $order->order_amount = $cartTotalAfterProductDiscountAndCouponDiscountExtraDiscount + $totalTaxAmount;
            $order->coupon_discount_amount = $couponDiscount ?? 0;
            $order->save();

            foreach ($order_details as $key => $item) {
                $order_details[$key]['order_id'] = $order->id;
            }
            $this->orderDetail->insert($order_details);
            if (!empty($order->user_id)) {
                $user = User::find($order->user_id);
                $userFcmToken = $user?->cm_firebase_token;
                $value = Helpers::order_status_update_message('delivered');
                try {
                    if ($value && $userFcmToken) {
                        $data = [
                            'title' => 'Order',
                            'description' => $value,
                            'order_id' => $order->id,
                            'image' => '',
                            'type' => 'order',
                        ];
                        Helpers::send_push_notif_to_device($userFcmToken, $data);
                    }

                    $emailServices = Helpers::get_business_settings('mail_config');
                    if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                        Mail::to($user->email)->send(new OrderPlaced($order->id));
                    }

                } catch (Exception $e) {}
            }
            if ($request->type == 'wallet' || $request->type == 'multiple') {
                $deductAmount = $request->type == 'wallet' ? $request->paid_amount: $walletBalance;
                $this->walletTransactionCreate(model: $customer, walletBalance: $walletBalance, amount: $deductAmount, type: 'wallet_payment', direction: 'debit');
            }
            DB::commit();
            session()->forget(['cart', 'customer_id', 'branch_id']);
            session(['last_order' => $order->id]);

            Toastr::success(translate('order_placed_successfully'));
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            Toastr::warning(translate('failed_to_place_order'));
            return back();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeKeys(Request $request): JsonResponse
    {
        session()->put($request['key'], $request['value']);
        if ($request['key'] == 'customer_id' && $request['value'] != null) {
            $customer = $this->user->where('id', $request['value'])->with('userAccount')->get()->map(function ($data) {
                return [...$data->toArray(), 'balance' => Helpers::set_symbol($data->userAccount->wallet_balance ?? 0)];
            })->first();
            return response()->json([
                'success' => 1,
                'customer' => $customer,
            ]);
        }

        return response()->json('', 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function generateInvoice($id): JsonResponse
    {
        $order = $this->order->where('id', $id)->first();

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos.order.invoice', compact('order'))->render(),
        ]);
    }

    public function customerStore(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
        ]);

        $userPhone = $this->user->where('phone', $request->phone)->first();
        if (isset($userPhone)) {
            Toastr::error(translate('The phone is already taken'));
            return back();
        }

        $userEmail = $this->user->where('email', $request->email)->first();
        if (isset($userEmail)) {
            Toastr::error(translate('The email is already taken'));
            return back();
        }

        $this->user->create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt('password'),
        ]);

        Toastr::success(translate('customer added successfully'));
        return back();
    }

    public function exportOrders(Request $request): StreamedResponse|string
    {
        $queryParams = [];
        $search = $request['search'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $query = $this->order->pos()->with(['customer'])->where(['branch_id' => auth('branch')->id()])
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        $queryParams = ['start_date' => $startDate, 'end_date' => $endDate];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $queryParams = ['search' => $request['search']];
        }

        $orders = $query->orderBy('id', 'desc')->get();
        $storage = $orders->map(function ($order) {
            $branch     = $order?->branch?->name ?? 'Branch Deleted';
            $customer   = $order->user_id == 0 && $order->user_id == null
                ? 'Walk In Customer'
                : ($order->customer
                    ? $order->customer->f_name . ' ' . $order->customer->l_name
                    : 'Customer Deleted');
            return [
                'Order Id' => $order['id'],
                'Order Date' => date('d M Y',strtotime($order['created_at'])),
                'Customer' => $customer,
                'Branch'=> $branch,
                'Order Amount' => $order['order_amount'],
                'Order Status' => $order['order_status'],
                'Order Type' => $order['order_type'],
                'Payment Status' => $order['payment_status'],
                'Payment Method' => $order['payment_method'],
            ];
        });
        return (new FastExcel($storage))->download('pos-orders.xlsx');
    }

    public function getCustomerCouponList(Request $request): JsonResponse
    {
        $coupons = $this->couponList($request->session()->get('customer_id'));

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos.partials.coupon-list', compact('coupons'))->render(),
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pos_coupon_code' => 'required|string|exists:coupons,code',
        ],[
            'pos_coupon_code.required' => translate('coupon_code_is_required'),
            'pos_coupon_code.exists' => translate('coupon_not_found')
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $coupons = $this->couponList(session()->get('customer_id'));
        $coupon = $coupons->firstWhere('code', $request->pos_coupon_code);
        $cart = session()->get('cart', collect());

        if ($cart->isEmpty()) {
            return response()->json(['errors' => [
                ['code' => 'pos_coupon_code', 'message' => translate('cart_is_empty')]
            ]]);
        }

        if (!isset($coupon)) {
            return response()->json(['errors' => [
                ['code' => 'pos_coupon_code', 'message' => translate('coupon_not_found')]
            ]]);
        }

        if ($coupon->discount_type == 'first_order') {
            $totalOrder = $this->order->where('user_id', session()->get('customer_id'))->count();
            if ($totalOrder > 0) {
                return response()->json(['errors' => [
                    ['code' => 'pos_coupon_code', 'message' => translate('this_coupon_is_only_for_first_order')]
                ]]);
            }
        }

        $cartSubTotalAfterDiscount =0;

        if ($cart->isNotEmpty()) {
            $cartItems = $cart->filter(fn($value, $key) => is_array($value))->values();
            $discount_on_product = 0;
            $subtotal = 0;
            foreach ($cartItems as $cartItem) {
                $product_subtotal = ($cartItem['price']) * $cartItem['quantity'];
                $discount_on_product += ($cartItem['discount'] * $cartItem['quantity']);
                $subtotal += $product_subtotal;
            }

            $cartSubTotalAfterDiscount = $subtotal - $discount_on_product;
        }

        if ($coupon->min_purchase > $cartSubTotalAfterDiscount) {
            return response()->json(['errors' => [
                ['code' => 'pos_coupon_code', 'message' => translate('minimum_purchase_amount_for_this_coupon_is') . ' ' . Helpers::set_symbol($coupon->min_purchase)]
            ]]);
        }

        $couponDiscountAmount = 0;
        if ($coupon->discount_type == 'amount') {
            $couponDiscountAmount = $coupon->discount;
            if ($couponDiscountAmount > $cartSubTotalAfterDiscount) {
                $couponDiscountAmount = $cartSubTotalAfterDiscount;
            }
        }

        if ($coupon->discount_type == 'percent') {
            $couponDiscountAmount = (($cartSubTotalAfterDiscount * $coupon->discount) / 100);
            if ($couponDiscountAmount > $coupon->max_discount) {
                $couponDiscountAmount = $coupon->max_discount;
            }
        }


        $cart = $request->session()->get('cart', collect([]));
        $cart['coupon_discount'] = $couponDiscountAmount;
        $cart['coupon_code'] = $coupon->code;
        $afterCouponDiscountPrice = $cartSubTotalAfterDiscount - $couponDiscountAmount;

        if ($afterCouponDiscountPrice <= 0) {
            unset($cart['extra_discount'], $cart['extra_discount_type']);
        }
        if ($cart->has('extra_discount_type')) {
            if ($cart['extra_discount_type'] == 'amount') {
                $extra_discount = $cart['extra_discount'];
                if ($extra_discount > $afterCouponDiscountPrice) {
                    $extra_discount = $afterCouponDiscountPrice;
                }
                $cart['extra_discount'] = $extra_discount;
                $cart['extra_discount_type'] ='amount';
            }
        }
        $request->session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'success_message' => translate('Coupon_discount_applied')]);
        }

        Toastr::success(translate('Coupon_discount_applied_successfully'));
        return back();
    }

    public function deleteCoupon(Request $request): RedirectResponse|JsonResponse
    {
        $cart = session()->get('cart', collect());
        if ($cart->isNotEmpty())
        {
            unset($cart['coupon_discount'], $cart['coupon_code']);
        }

        if ($request->ajax())
        {
            return response()->json(['success' => true, 'success_message' => translate('Coupon discount removed successfully')]);
        }

        Toastr::success(translate('Coupon discount removed successfully'));
        return back();
    }

    public function getCartPaymentSection(Request $request)
    {
        $customerWalletBalance = $request->customer_wallet_balance;
        $cartTotalAmount = $request->total_amount;
        $paymentType = $request->payment_type;

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos.partials.cart-payment-section', compact('customerWalletBalance', 'cartTotalAmount', 'paymentType'))->render(),
        ]);
    }

    private function returnError(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'errors' => [
                    ['code' => 'discount_error', 'message' => translate($message)]
                ]
            ]);
        }

        Toastr::error(translate($message));
        return back();
    }

    private function couponList($customerId): Collection
    {
        if (is_null($customerId) || $customerId == 0) {
            return collect();
        }

        $totalOrders = $this->order->where('user_id', $customerId)->count();

        return $this->coupon
            ->withCount(['orders as used_count' => function ($query) use ($customerId) {
                $query->where('user_id', $customerId);
            }])
            ->active()
            ->get()
            ->filter(function($item) use ($totalOrders) {
                if ($item->coupon_type == 'first_order') {
                    return $totalOrders == 0 && $item->used_count < 1;
                }
                return $item->used_count < $item->limit;
            })
            ->values();
    }
}
