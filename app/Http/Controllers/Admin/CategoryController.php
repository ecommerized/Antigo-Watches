<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Translation;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use UploadSizeHelperTrait;

    public function __construct(
        private Category $category,
        private Translation $translation
    ){
        $this->initUploadLimits();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): Factory|View|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search  = $request->query('search');
        $queryParams = ['per_page' => $perPage];

        $query = $this->category->where('position', 0);


        if ($search) {
            $queryParams['search'] = $search;

            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()
            ->paginate($perPage)
            ->appends($queryParams);

        return view('admin-views.category.index', compact('categories', 'search', 'perPage'));
    }


    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function subIndex(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search  = $request->query('search');
        $queryParams = ['per_page' => $perPage];
        $query = $this->category
            ->with('parent')
            ->where('position', 1);

        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()
            ->paginate($perPage)
            ->appends($queryParams);

        return view('admin-views.category.sub-index', compact('categories', 'search', 'perPage'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function store(Request $request): RedirectResponse|JsonResponse
    {
        $check = $this->validateUploadedFile($request, ['image','banner_image']);
        if ($check !== true) {
            return $check;
        }

        $validator = Validator::make($request->all(), [
            'name.0' => 'required|string|max:255',
            'name.*' => 'max:255',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'banner_image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'name.0.required' => $request->parent_id == null
                ? translate('Category name is required!')
                : translate('Sub category name is required!'),
            'name.*.max' => $request->parent_id == null
                ? translate('Category name should not exceed 255 characters')
                : translate('Sub category name should not exceed 255 characters'),
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
            'banner_image.mimes' => 'Banner image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'banner_image.max' => translate('Banner image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        foreach ($request->name as $name) {
            $existingCat = $this->category
                ->where('name', $name)
                ->where('parent_id', $request->parent_id ?? 0)
                ->first();

            if ($existingCat) {
                return response()->json([
                    'errors' => [
                        [
                            'code' => 'name.0',
                            'message' => $request->parent_id == null
                                ? translate('Category already exists!')
                                : translate('Sub-category already exists!')
                        ]
                    ]
                ]);
            }
        }


        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('category/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = 'def.png';
        }
        if (!empty($request->file('banner_image'))) {
            $banner_image_name = Helpers::upload('category/banner/', APPLICATION_IMAGE_FORMAT, $request->file('banner_image'));
        } else {
            $banner_image_name = 'def.png';
        }

        $category = $this->category;
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->image = $image_name;
        $category->banner_image = $banner_image_name;
        $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
        $category->position = $request->position;
        $category->save();

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => Category::class,
                    'translationable_id' => $category->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
        }
        if (count($data)) {
            $this->translation->insert($data);
        }

        if ($request->ajax())
        {
            return response()->json([], 200);
        }

        Toastr::success($request->parent_id == 0 ? translate('Category Added Successfully!') : translate('Sub Category Added Successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $category = $this->category->withoutGlobalScopes()->with('translations')->find($id);
        return view('admin-views.category.edit', compact('category'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        $category->status = $request->status;
        $category->save();
        Toastr::success($category->parent_id == 0 ? translate('Category status updated!') : translate('Sub Category status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        $check = $this->validateUploadedFile($request, ['image', 'banner_image']);
        if ($check !== true) {
            return $check;
        }

        $validator = Validator::make($request->all(), [
            'name.0' => 'required|string|max:255',
            'name.*' => 'max:255',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'banner_image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),

        ], [
            'name.0.required' => $request->parent_id == null
                ? translate('Category name is required!')
                : translate('Sub category name is required!'),
            'name.*.max' => $request->parent_id == null
                ? translate('Category name should not exceed 255 characters')
                : translate('Sub category name should not exceed 255 characters'),
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
            'banner_image.mimes' => 'Banner image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'banner_image.max' => translate('Banner image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        foreach ($request->name as $name) {
            $existingCat = $this->category
                ->where('name', $name)
                ->where('parent_id', $request->parent_id ?? 0)
                ->where('id', '!=', $id)
                ->first();

            if ($existingCat) {
                return response()->json([
                    'errors' => [
                        [
                            'code' => 'name.0',
                            'message' => $request->parent_id == null
                                ? translate('Category already exists!')
                                : translate('Sub category already exists!')
                        ]
                    ]
                ]);
            }
        }

        $category = $this->category->find($id);
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
        $category->image = $request->has('image') ? Helpers::update('category/', $category->image, APPLICATION_IMAGE_FORMAT, $request->file('image')) : $category->image;
        $category->banner_image = $request->has('banner_image') ? Helpers::update('category/banner/', $category->banner_image, APPLICATION_IMAGE_FORMAT, $request->file('banner_image')) : $category->banner_image;
        $category->save();
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $this->translation->updateOrInsert(
                    ['translationable_type' => Category::class,
                        'translationable_id' => $category->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
        }
        if ($request->ajax())
        {
            return response()->json([], 200);
        }
        Toastr::success($category->parent_id == 0 ? translate('Category updated successfully!') : translate('Sub Category updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);

        if ($category->childes->count() == 0) {
            if (Storage::disk('public')->exists('category/' . $category['image'])) {
                Storage::disk('public')->delete('category/' . $category['image']);
            }
            $category->delete();
            Toastr::success($category->parent_id == 0 ? translate('Category removed!') : translate('Sub Category removed!'));
        } else {
            Toastr::warning($category->parent_id == 0 ? translate('Remove subcategories first!') : translate('Sub Remove subcategories first!'));
        }
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function featured(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        $category->is_featured = $request->featured;
        $category->save();
        Toastr::success(translate('Featured status updated!'));
        return back();
    }
}
