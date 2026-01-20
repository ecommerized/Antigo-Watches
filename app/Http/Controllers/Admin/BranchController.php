<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryChargeSetup;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Branch $branch,
        private DeliveryChargeSetup $deliveryChargeSetup
    ){
        $this->initUploadLimits();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        return view('admin-views.branch.index');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): Factory|View|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->branch;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $branches = $query->orderBy('id', 'desc')->paginate($perPage)->appends($queryParams);
        return view('admin-views.branch.list', compact('branches','search','perPage'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $check = $this->validateUploadedFile($request, ['image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'name' => 'required|max:255|unique:branches',
            'email' => 'required|max:255|unique:branches',
            'password' => 'required|min:8|max:255',
            'image' => 'required|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'name.required' => 'Name is required!',
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('branch/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $defaultBranch = $this->branch->find(1);
        $defaultLat = $defaultBranch->latitude ?? '23.777176';
        $defaultLong = $defaultBranch->longitude ?? '90.399452';
        $defaultCoverage = $defaultBranch->coverage ?? 100;

        $branch = $this->branch;
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->latitude = $request->latitude ?? $defaultLat;
        $branch->longitude = $request->longitude ?? $defaultLong;
        $branch->coverage = $request->coverage ?? $defaultCoverage;

        $branch->address = $request->address;
        $branch->password = bcrypt($request->password);
        $branch->image = $image_name;
        $branch->phone = $request->number;
        $branch->save();

        $branchDeliveryCharge = $this->deliveryChargeSetup;
        $branchDeliveryCharge->branch_id = $branch->id;
        $branchDeliveryCharge->delivery_charge_type = 'fixed';
        $branchDeliveryCharge->fixed_delivery_charge = 0;
        $branchDeliveryCharge->save();

        Toastr::success(translate('Branch added successfully!'));
        return redirect()->route('admin.branch.list');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $branch = $this->branch->find($id);
        return view('admin-views.branch.edit', compact('branch'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $check = $this->validateUploadedFile($request, ['image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'email' => ['required', 'unique:branches,email,'.$id.',id'],
            'name' => ['required', 'unique:branches,name,'.$id.',id'],
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'name.required' => 'Name is required!',
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        $branch = $this->branch->find($id);
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->longitude = $request->longitude ? $request->longitude : $branch->longitude;
        $branch->latitude = $request->latitude ? $request->latitude : $branch->latitude;
        $branch->coverage = $request->coverage ? $request->coverage : $branch->coverage;
        $branch->address = $request->address;
        $branch->phone = $request->number;
        $branch->image = $request->has('image') ? Helpers::update('branch/', $branch->image, APPLICATION_IMAGE_FORMAT, $request->file('image')) : $branch->image;
        if ($request['password'] != null) {
            $branch->password = bcrypt($request->password);
        }
        $branch->save();
        Toastr::success(translate('Branch updated successfully!'));
        return redirect()->route('admin.branch.list');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $branch = $this->branch->find($request->id);
        $branch->delete();
        Toastr::success(translate('Branch removed!'));
        return back();
    }

    public function status(Request $request): RedirectResponse
    {
        $branch = $this->branch->find($request->id);
        $branch->status = $request->status;
        $branch->save();
        Toastr::success(translate('Branch status updated!'));
        return back();
    }
}
