<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class BannerController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Banner $banner,
        private Category $category,
        private Product $product
    ){
        $this->initUploadLimits();
    }

    /**
     * @return Application|Factory|View
     */
    function index(Request $request): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->banner;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('title', 'like', "%{$search}%");
            });
        }

        $banners = $query->latest()->paginate($perPage)->appends($queryParams);

        $products = $this->product->orderBy('name')->get();
        $categories = $this->category->where(['parent_id' => 0])->orderBy('name')->get();
        return view('admin-views.banner.index', compact('products', 'categories', 'banners', 'search','perPage'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    function list(Request $request): View|Factory|Application
    {
        $search = $request->input('search');
        $queryParam = ['search' => $search];

        if ($search) {
            $keywords = explode(' ', $search);
            $banners = $this->banner->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('title', 'like', "%{$keyword}%");
                }
            });
        } else {
            $banners = $this->banner;
        }

        $banners = $banners->latest()->paginate(Helpers::pagination_limit())->appends($queryParam);
        return view('admin-views.banner.list', compact('banners', 'search'));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $check = $this->validateUploadedFile($request, ['primary_image','secondary_image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'title' => 'required|max:255',
            'banner_type' => 'required|in:primary,secondary',
            'primary_image' => 'required_if:banner_type,primary|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'secondary_image' => 'required_if:banner_type,secondary|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'title.max' => 'Title is too long',
            'primary_image.mimes' => 'Primary image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'primary_image.max' => translate('Primary image size must be below ' . $this->maxImageSizeReadable),
            'secondary_image.mimes' => 'Secondary image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'secondary_image.max' => translate('Secondary image size must be below ' . $this->maxImageSizeReadable),
        ]);

        $banner = $this->banner;
        $banner->title = $request->title;
        $banner->banner_type = $request->banner_type;
        if ($request['item_type'] == 'product') {
            $banner->product_id = $request->product_id;
        } elseif ($request['item_type'] == 'category') {
            $banner->category_id = $request->category_id;
        }
        $banner->image = $request->banner_type == 'primary' ? Helpers::upload('banner/', APPLICATION_IMAGE_FORMAT, $request->file('primary_image')) : Helpers::upload('banner/', APPLICATION_IMAGE_FORMAT, $request->file('secondary_image'));
        $banner->save();
        Toastr::success(translate('Banner added successfully!'));
        return redirect('admin/banner/add-new');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $products = $this->product->orderBy('name')->get();
        $banner = $this->banner->find($id);
        $categories = $this->category->where(['parent_id' => 0])->orderBy('name')->get();
        return view('admin-views.banner.edit', compact('banner', 'products', 'categories'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $banner = $this->banner->find($request->id);
        $banner->status = $request->status;
        $banner->save();
        Toastr::success(translate('Banner status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Request $request, $id): Redirector|RedirectResponse|Application
    {
        $check = $this->validateUploadedFile($request, ['primary_image','secondary_image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'title' => 'required|max:255',
            'banner_type' => 'required|in:primary,secondary',
            'primary_image' => 'image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'secondary_image' => 'image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'title.required' => 'Title is required!',
            'primary_image.mimes' => 'Primary image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'primary_image.max' => translate('Primary image size must be below ' . $this->maxImageSizeReadable),
            'secondary_image.mimes' => 'Secondary image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'secondary_image.max' => translate('Secondary image size must be below ' . $this->maxImageSizeReadable),
        ]);

        $banner = $this->banner->find($id);
        $banner->title = $request->title;
        $banner->banner_type = $request->banner_type;
        if ($request['item_type'] == 'product') {
            $banner->product_id = $request->product_id;
            $banner->category_id = null;
        } elseif ($request['item_type'] == 'category') {
            $banner->product_id = null;
            $banner->category_id = $request->category_id;
        }

        if($request->banner_type == 'primary'){
            $banner->image = $request->has('primary_image') ? Helpers::update('banner/', $banner->image, APPLICATION_IMAGE_FORMAT, $request->file('primary_image')) : $banner->image;
        }else{
            $banner->image = $request->has('secondary_image') ? Helpers::update('banner/', $banner->image, APPLICATION_IMAGE_FORMAT, $request->file('secondary_image')) : $banner->image;
        }

        $banner->save();
        Toastr::success(translate('Banner updated successfully!'));
        return redirect()->route('admin.banner.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $banner = $this->banner->find($request->id);
        Helpers::delete('banner/' . $banner['image']);
        $banner->delete();
        Toastr::success(translate('Banner removed!'));
        return back();
    }
}
