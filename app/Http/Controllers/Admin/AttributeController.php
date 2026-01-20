<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    public function __construct(
        private Attribute $attribute,
        private Translation $translation
    ) {}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search  = $request->query('search');

        $attributes = $this->attribute;

        if ($search) {
            $queryParams['search'] = $search;
            $attributes = $attributes->where(function ($query) use ($search) {
                    $query->orWhere('name', 'like', "%{$search}%");
            });
        }

        $attributes = $attributes
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($queryParams);

        return view('admin-views.attribute.index', compact('attributes', 'search', 'perPage'));
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name.0' => 'required|string|max:255|unique:attributes,name',
            'name.*' => 'max:255',
        ], [
            'name.0.required' =>  translate('Attribute name is required!'),
            'name.*.max' => translate('Attribute name should not exceed 255 characters'),
            'name.0.unique' => translate('Attribute name already exists!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $attribute = new Attribute;
        $attribute->name = $request->name[array_search('en', $request->lang)];
        $attribute->save();

        $data = [];
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                $data[] = array(
                    'translationable_type' => Attribute::class,
                    'translationable_id' => $attribute->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
        }
        if(count($data))
        {
            $this->translation->insert($data);
        }

        if ($request->ajax())
        {
            return response()->json([], 200);
        }

        Toastr::success(translate('Attribute added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $attribute = $this->attribute->withoutGlobalScopes()->with('translations')->find($id);
        return view('admin-views.attribute.edit', compact('attribute'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name.0' => 'required|string|max:255|unique:attributes,name,'.$id,
            'name.*' => 'max:255',
        ], [
            'name.0.required' =>  translate('Attribute name is required!'),
            'name.*.max' => translate('Attribute name should not exceed 255 characters'),
            'name.0.unique' => translate('Attribute name already exists!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $attribute = $this->attribute->find($id);
        $attribute->name = $request->name[array_search('en', $request->lang)];
        $attribute->save();

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                $this->translation->updateOrInsert(
                    ['translationable_type'  => Attribute::class,
                        'translationable_id'    => $attribute->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }


        if ($request->ajax())
        {
            return response()->json([], 200);
        }
        Toastr::success(translate('Attribute updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $attribute = $this->attribute->find($request->id);
        $attribute->delete();
        Toastr::success(translate('Attribute removed!'));
        return back();
    }
}
