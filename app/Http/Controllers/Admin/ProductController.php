<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Translation;
use App\Traits\UploadSizeHelperTrait;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Category $category,
        private Product $product,
        private Review $review,
        private Translation $translation
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function variantCombination(Request $request): JsonResponse
    {
        $options = [];
        $price = $request->price;
        $productName = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;
        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'productName'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCategories(Request $request): JsonResponse
    {
        $categories = $this->category->where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select subcategory---</option>';
        foreach ($categories as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $categories = $this->category->where(['position' => 0])->get();
        return view('admin-views.product.index', compact('categories'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->product;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }
        $products = $query->latest()->paginate($perPage)->appends($queryParams);
        return view('admin-views.product.list', compact('products', 'search','perPage'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $products = $this->product->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.product.partials._table', compact('products'))->render()
        ]);
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function view($id): Factory|View|Application
    {
        $product = $this->product->where(['id' => $id])->first();
        $reviews = $this->review->where(['product_id' => $id])->latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['images']);
        if ($check !== true) {
            return $check;
        }

        $validator = Validator::make($request->all(), [
            'name.0' => 'required|unique:products,name',
            'description.0' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'unit' => 'required|string|in:kg,gm,ltr,pc',
            'tax' => 'required|numeric',
            'tax_type' => 'required|string|in:percent,amount',
            'discount' => 'required|numeric',
            'discount_type' => 'required|string|in:percent,amount',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'total_stock' => 'required|numeric|min:1',
            'images'   => 'required|array|min:1',
            'images.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'choice_no' => 'sometimes|array',
            'choice.*'  => 'sometimes|string',
            'choice_options_*' => 'sometimes|array',
        ], [
            'name.0.required' => 'Product name is required!',
            'category_id.required' => 'Category  is required!',
            'images.*.max' => 'Each image may not be greater than ' . $this->maxImageSizeReadable . '.',
            'images.*.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'images.*' => 'image'
        ]);

        if ($request['discount_type'] == 'percent') {
            $discount = ($request['price'] / 100) * $request['discount'];
        } else {
            $discount = $request['discount'];
        }

        if ($request['price'] <= $discount) {
            $validator->getMessageBag()->add('discount', 'Discount can not be more or equal to the price!');
        }

        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'price_')) {
                $validator->sometimes($key, 'required|numeric|min:0', function () use ($value) {
                    return true;
                });
            }
            if (Str::startsWith($key, 'stock_')) {
                $validator->sometimes($key, 'required|integer|min:0', function () use ($value) {
                    return true;
                });
            }
        }

        $imageName = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image_data = Helpers::upload('product/', APPLICATION_IMAGE_FORMAT, $img);
                $imageName[] = $image_data;
            }
            $image_data = json_encode($imageName);
        } else {
            $image_data = json_encode([]);
        }

        $product= new Product;
        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $product->category_ids = json_encode($category);
        $product->description = $request->description[array_search('en', $request->lang)];

        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add($str, 'Attribute choice option values can not be empty!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }

        $product->choice_options = json_encode($choiceOptions);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);

        $stockCount = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                $variations[] = $item;
                $stockCount += $item['stock'];
            }
        } else {
            $stockCount = (integer)$request['total_stock'];
        }

        if ((integer)$request['total_stock'] != $stockCount) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->unit = $request->unit;
        $product->image = $image_data;

        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;

        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;
        $product->total_stock = $request->total_stock;

        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->save();

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => Product::class,
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
            if ($request->description[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => Product::class,
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                );
            }
        }

        $this->translation->insert($data);

        return response()->json([], 200);
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): Factory|View|Application
    {
        $product = $this->product->withoutGlobalScopes()->with('translations')->find($id);
        $product_category = json_decode($product->category_ids);
        $categories = $this->category->where(['parent_id' => 0])->get();
        return view('admin-views.product.edit', compact('product', 'product_category', 'categories'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->status = $request->status;
        $product->save();
        Toastr::success(translate('Product status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['images']);
        if ($check !== true) {
            return $check;
        }

        $validator = Validator::make($request->all(), [
            'name.0' => 'required|unique:products,name,' . $id,
            'description.0' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'unit' => 'required|string|in:kg,gm,ltr,pc',
            'tax' => 'required|numeric',
            'tax_type' => 'required|string|in:percent,amount',
            'discount' => 'required|numeric',
            'discount_type' => 'required|string|in:percent,amount',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'total_stock' => 'required|numeric|min:1',
            'images'   => 'sometimes|array|min:1',
            'images.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'choice_no' => 'sometimes|array',
            'choice.*'  => 'sometimes|string',
            'choice_options_*' => 'sometimes|array',
        ], [
            'name.0.required' => 'Product name is required!',
            'category_id.required' => 'Category  is required!',
            'images.*.max' => 'Each image may not be greater than ' . $this->maxImageSizeReadable . '.',
            'images.*.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'images.*' => 'image'
        ]);

        if ($request['discount_type'] == 'percent') {
            $discount = ($request['price'] / 100) * $request['discount'];
        } else {
            $discount = $request['discount'];
        }

        if ($request['price'] <= $discount) {
            $validator->getMessageBag()->add('discount', 'Discount can not be more or equal to the price!');
        }

        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'price_')) {
                $validator->sometimes($key, 'required|numeric|min:0', function () use ($value) {
                    return true;
                });
            }
            if (Str::startsWith($key, 'stock_')) {
                $validator->sometimes($key, 'required|integer|min:0', function () use ($value) {
                    return true;
                });
            }
        }

        $product = $this->product->find($id);
        $images = json_decode($product->image);
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image_data = Helpers::upload('product/', APPLICATION_IMAGE_FORMAT, $img);
                $images[] = $image_data;
            }

        }

        if (!count($images)) {
            $validator->getMessageBag()->add('images', 'Image can not be empty!');
        }

        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $product->category_ids = json_encode($category);
        $product->description = $request->description[array_search('en', $request->lang)];

        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add($str, 'Attribute choice option values can not be empty!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }
        $product->choice_options = json_encode($choiceOptions);
        $variations = [];
        $options = [];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        $stockCount = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                $variations[] = $item;
                $stockCount += $item['stock'];
            }
        } else {
            $stockCount = (integer)$request['total_stock'];
        }

        if ((integer)$request['total_stock'] != $stockCount) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->unit = $request->unit;
        $product->image = json_encode($images);

        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;

        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;
        $product->total_stock = $request->total_stock;

        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->save();


        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $this->translation->updateOrInsert(
                    ['translationable_type' => Product::class,
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                $this->translation->updateOrInsert(
                    ['translationable_type' => Product::class,
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'description'],
                    ['value' => $request->description[$index]]
                );
            }
        }

        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        foreach (json_decode($product['image'], true) as $img) {
            if (Storage::disk('public')->exists('product/' . $img)) {
                Storage::disk('public')->delete('product/' . $img);
            }
        }
        $product->delete();
        Toastr::success(translate('Product removed!'));
        return back();
    }

    /**
     * @param $id
     * @param $name
     * @return RedirectResponse
     */
    public function removeImage($id, $name): RedirectResponse
    {
        $product = $this->product->find($id);
        $imageArray = [];
        foreach (json_decode($product['image'], true) as $img) {
            if (strcmp($img, $name) != 0) {
                $imageArray[] = $img;
            }
        }

        if (count($imageArray) == 0) {
            Toastr::warning(translate('Product must have at least one image!'));
            return back();
        }

        if (Storage::disk('public')->exists('product/' . $name)) {
            Storage::disk('public')->delete('product/' . $name);
        }

        $this->product->where(['id' => $id])->update([
            'image' => json_encode($imageArray)
        ]);

        Toastr::success(translate('Image removed successfully!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function bulkImportIndex(): Factory|View|Application
    {
        return view('admin-views.product.bulk-import');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkImportProduct(Request $request): RedirectResponse
    {
        $this->initUploadLimits('file');
        $check = $this->validateUploadedFile($request, ['products_file']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'products_file' => 'required|mimes:xlsx|max:' . $this->maxImageSizeKB
        ],
            [
                'products_file.required' => 'The Product file field is empty',
                'products_file.mimes' => 'File type must be xlsx',
                'products_file.max' => 'File size must be less than ' . $this->maxImageSizeReadable
            ]
        );

        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('You have uploaded a wrong format file, please upload the right file.'));
            return back();
        }

        $col_key = ['name','description','price','tax','category_id','sub_category_id','discount','discount_type','tax_type','unit','total_stock'];
        foreach ($collections as $key => $collection) {

            foreach ($collection as $collectionKey => $value) {
                if ($collectionKey != "" && !in_array($collectionKey, $col_key)) {
                    Toastr::error('Please upload the correct format file.');
                    return back();
                }
            }
        }

        $data = [];

        foreach ($collections as $key => $collection) {
            if ($collection['name'] === "") {
                Toastr::error('Please fill name field of row ' . ($key + 2));
                return back();
            }
            if ($collection['description'] === "") {
                Toastr::error('Please fill description field of row ' . ($key + 2));
                return back();
            }
            if ($collection['price'] === "") {
                Toastr::error('Please fill price field of row ' . ($key + 2));
                return back();
            }
            if ($collection['tax'] === "") {
                Toastr::error('Please fill tax field of row ' . ($key + 2));
                return back();
            }
            if ($collection['category_id'] === "") {
                Toastr::error('Please fill category_id field of row ' . ($key + 2));
                return back();
            }
            if ($collection['sub_category_id'] === "") {
                Toastr::error('Please fill sub_category_id field of row ' . ($key + 2));
                return back();
            }
            if ($collection['discount'] === "") {
                Toastr::error('Please fill discount field of row ' . ($key + 2));
                return back();
            }
            if ($collection['discount_type'] === "") {
                Toastr::error('Please fill discount_type field of row ' . ($key + 2));
                return back();
            }
            if ($collection['tax_type'] === "") {
                Toastr::error('Please fill tax_type field of row ' . ($key + 2));
                return back();
            }
            if ($collection['unit'] === "") {
                Toastr::error('Please fill unit field of row ' . ($key + 2));
                return back();
            }
            if ($collection['total_stock'] === "") {
                Toastr::error('Please fill total_stock field of row ' . ($key + 2));
                return back();
            }

            if (!is_numeric($collection['price'])) {
                Toastr::error('Price of row ' . ($key + 2) . ' must be number');
                return back();
            }

            if (!is_numeric($collection['discount'])) {
                Toastr::error('Discount of row ' . ($key + 2) . ' must be number');
                return back();
            }

            if (!is_numeric($collection['tax'])) {
                Toastr::error('Tax of row ' . ($key + 2) . ' must be number');
                return back();
            }

            if (!is_numeric($collection['total_stock'])) {
                Toastr::error('Total stock of row ' . ($key + 2) . ' must be number');
                return back();
            }

            $product = [
                'discount_type' => $collection['discount_type'],
                'discount' => $collection['discount'],
            ];
            if ($collection['price'] <= Helpers::discount_calculate($product, $collection['price'])) {
                Toastr::error('Discount can not be more or equal to the price!');
                return back();
            }
        }

        foreach ($collections as $collection) {
            $data[] = [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'image' => json_encode(['def.png']),
                'price' => $collection['price'],
                'variations' => json_encode([]),
                'tax' => $collection['tax'],
                'status' => 1,
                'attributes' => json_encode([]),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 0], ['id' => (string)$collection['sub_category_id'], 'position' => 1]]),
                'choice_options' => json_encode([]),
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'unit' => $collection['unit'],
                'total_stock' => $collection['total_stock'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('products')->insert($data);
        Toastr::success(count($data) . translate('_Products imported successfully'));
        return back();
    }

    /**
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function bulkExportProduct(): StreamedResponse|string
    {
        $storage = [];
        $products = $this->product->all();

        foreach ($products as $item) {
            $categoryId = 0;
            $subCategoryId = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $categoryId = $category['id'];
                } else if ($category['position'] == 2) {
                    $subCategoryId = $category['id'];
                }
            }

            if (!isset($item->name)) {
                $item->name = 'Unnamed Product';
            }

            if (!isset($item->description)) {
                $item->description = 'No description available';
            }

            $storage[] = [
                'name' => $item->name,
                'description' => $item->description,
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'price' => $item->price,
                'tax' => $item->tax,
                'status' => $item->status,
                'discount' => $item->discount,
                'discount_type' => $item->discount_type,
                'tax_type' => $item->tax_type,
                'unit' => $item->unit,
                'total_stock' => $item->total_stock,
            ];
        }

        return (new FastExcel($storage))->download('products.xlsx');
    }
}
