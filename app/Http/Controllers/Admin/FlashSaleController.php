<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FlashSaleController extends Controller
{
    public function __construct(
        private FlashSale $flashSale,
        private Product $product,
        private FlashSaleProduct $flashSaleProduct,
    )
    {}

    public function index(Request $request)
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->flashSale;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('title', 'like', "%{$search}%");
            });
        }

        $flashSales = $query->withCount('products')->latest()->paginate($perPage)->appends($queryParams);

        return view('admin-views.flash-sale.index', compact('flashSales', 'search','perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ],[
            'title.required'=>translate('Title is required'),
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('flash-sale/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $flashSale = $this->flashSale;
        $flashSale->title = $request->title;
        $flashSale->start_date = $request->start_date;
        $flashSale->end_date = $request->end_date;
        $flashSale->status = 0;
        $flashSale->image = $image_name;
        $flashSale->save();
        Toastr::success(translate('Added successfully!'));
        return back();
    }

    public function status(Request $request)
    {
        $this->flashSale->where(['status' => 1])->update(['status' => 0]);
        $flashSale = $this->flashSale->find($request->id);
        $flashSale->status = $request->status;
        $flashSale->save();
        Toastr::success(translate('Status updated!'));
        return back();
    }

    public function edit($id)
    {
        $flashSale = $this->flashSale->find($id);
        return view('admin-views.flash-sale.edit', compact('flashSale'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ],[
            'title.required'=>translate('Title is required'),
        ]);

        $flashSale = $this->flashSale->find($id);
        $flashSale->title = $request->title;
        $flashSale->start_date = $request->start_date;
        $flashSale->end_date = $request->end_date;
        $flashSale->image = $request->has('image') ? Helpers::update('flash-sale/', $flashSale->image, 'png', $request->file('image')) : $flashSale->image;
        $flashSale->save();
        Toastr::success(translate('Updated successfully!'));
        return redirect()->route('admin.flash-sale.index');
    }

    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $flashSale = $this->flashSale->find($request->id);
        if (Storage::disk('public')->exists('flash-sale/' . $flashSale['image'])) {
            Storage::disk('public')->delete('flash-sale/' . $flashSale['image']);
        }
        $flash_sale_ids = $this->flashSaleProduct->where(['flash_sale_id' => $request->id])->pluck('flash_sale_id');
        $flashSale->delete();
        $this->flashSaleProduct->whereIn('flash_sale_id', $flash_sale_ids)->delete();

        Toastr::success(translate('Flash sale deleted!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $flash_sale_id
     * @return Application|Factory|View
     */
    public function addProduct(Request $request, $flash_sale_id): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');
        $flashSaleProductIds = $this->flashSaleProduct->where('flash_sale_id', $flash_sale_id)->pluck('product_id');

        // Start query
        $query = $this->product->whereIn('id', $flashSaleProductIds);

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $flashSaleProducts = $query->paginate($perPage)->appends($queryParams);

        $products = $this->product->active()
            ->whereNotIn('id', $flashSaleProductIds)
            ->orderBy('id', 'DESC')->get();

        return view('admin-views.flash-sale.add-product', compact('products', 'flashSaleProducts','flash_sale_id', 'search', 'perPage'));
    }

    /**
     * @param Request $request
     * @param $flash_sale_id
     * @param $product_id
     * @return RedirectResponse
     */
    public function addProductToSession(Request $request, $flash_sale_id, $product_id): RedirectResponse
    {
        $product = $this->product->find($product_id);

        $flashSaleProduct = $this->flashSaleProduct
            ->where(['product_id' => $product_id, 'flash_sale_id' => $flash_sale_id])
            ->first();

        if (isset($flashSaleProduct)){
            Toastr::info($product['name']. ' is already exist in this flash sale!');
            return back();
        }

        $selectedProduct = [
            'flash_sale_id' => $flash_sale_id,
            'product_id' => $product->id,
            'name' => $product->name,
            'image' => $product['image_fullpath'][0],
            'price' => $product->price,
            'total_stock' => $product->total_stock,
        ];

        $request->session()->put('selected_product', $selectedProduct);

        // Retrieve the existing selected products from the session or an empty array if it doesn't exist
        $selectedProducts = $request->session()->get('selected_products', []);

        $productExists = false;
        foreach ($selectedProducts as $key => $existingProduct) {
            if ($existingProduct['product_id'] == $selectedProduct['product_id'] && $existingProduct['flash_sale_id'] == $selectedProduct['flash_sale_id']) {
                $productExists = true;
                Toastr::info($existingProduct['name']. ' is already selected!');
                break;
            }
        }

        if (!$productExists) {
            $selectedProducts[] = $selectedProduct;
        }

        $request->session()->put('selected_products', $selectedProducts);

        Toastr::success(translate('Product added successfully!'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $flash_sale_id
     * @param $product_id
     * @return RedirectResponse
     */
    public function deleteProductFromSession(Request $request, $flash_sale_id, $product_id): RedirectResponse
    {
        $selectedProducts = $request->session()->get('selected_products', []);

        foreach ($selectedProducts as $key => $product) {
            if ($product['flash_sale_id'] == $flash_sale_id && $product['product_id'] == $product_id) {
                unset($selectedProducts[$key]);
            }
        }

        // Re-index the array to remove gaps
        $selectedProducts = array_values($selectedProducts);
        $request->session()->put('selected_products', $selectedProducts);

        Toastr::success(translate('Product deleted successfully!'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $flash_sale_id
     * @return RedirectResponse
     */
    public function deleteAllProductsFromSession(Request $request, $flash_sale_id): RedirectResponse
    {
        $selectedProducts = $request->session()->get('selected_products', []);

        foreach ($selectedProducts as $key => $product) {
            if ($product['flash_sale_id'] == $flash_sale_id) {
                unset($selectedProducts[$key]);
            }
        }

        // Re-index the array to remove gaps
        $selectedProducts = array_values($selectedProducts);

        $request->session()->put('selected_products', $selectedProducts);

        Toastr::success(translate('Reset successfully!'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $flash_sale_id
     * @return RedirectResponse
     */
    public function flashSaleProductStore(Request $request, $flash_sale_id): RedirectResponse
    {
        $selectedProducts = $request->session()->get('selected_products', []);

        foreach ($selectedProducts as $key => $selectedProduct) {
            if ($selectedProduct['flash_sale_id'] == $flash_sale_id) {
                $existingProduct = $this->flashSaleProduct
                    ->where(['product_id' => $selectedProduct['product_id'], 'flash_sale_id' => $flash_sale_id])
                    ->first();

                if (!isset($existingProduct)){
                    FlashSaleProduct::create([
                        'product_id' => $selectedProduct['product_id'],
                        'flash_sale_id' => $flash_sale_id,
                    ]);
                }
                unset($selectedProducts[$key]);
            }
        }
        $selectedProducts = array_values($selectedProducts);

        $request->session()->put('selected_products', $selectedProducts);

        Toastr::success('Product added successfully!');
        return back();
    }


    /**
     * @param Request $request
     * @param $flash_sale_id
     * @param $product_id
     * @return RedirectResponse
     */
    public function deleteFlashProduct(Request $request, $flash_sale_id, $product_id): RedirectResponse
    {
        $this->flashSaleProduct->where(['product_id' => $product_id, 'flash_sale_id' => $flash_sale_id])->delete();

        Toastr::success('Product deleted successfully!');
        return back();
    }
}
