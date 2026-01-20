<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\DMReview;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DeliveryManController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private DeliveryMan $delivery_man,
        private DMReview $dm_review
    ){
        $this->initUploadLimits();
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('admin-views.delivery-man.index');
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
        $query = $this->delivery_man->with('branch')->where('application_status', 'approved');

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $deliveryMan = $query->latest()->paginate($perPage)->appends($queryParams);
        return view('admin-views.delivery-man.list', compact('deliveryMan', 'search','perPage'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function reviewsList(Request $request): Factory|View|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->dm_review;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->whereHas('delivery_man',function ($q) use ($search) {
                $q->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%");
            });
        }

        $reviews = $query->with(['delivery_man', 'customer'])->latest()->paginate($perPage)->appends($queryParams);
        return view('admin-views.delivery-man.reviews-list', compact('reviews', 'search','perPage'));
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function preview($id): Factory|View|Application
    {
        $deliveryMan = $this->delivery_man->with(['reviews'])->where(['id' => $id])->first();
        $reviews = $this->dm_review->where(['delivery_man_id' => $id])->latest()->paginate(20);
        return view('admin-views.delivery-man.view', compact('deliveryMan', 'reviews'));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request): Redirector|Application|RedirectResponse
    {
        $check = $this->validateUploadedFile($request, ['image', 'identity_image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'f_name' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:delivery_men',
            'phone' => 'required|unique:delivery_men',
            'password' => 'required|min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'identity_image' => 'sometimes|array',
            'identity_image.*' => 'image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'f_name.required' => translate('First name is required!'),
            'email.required' => translate('Email is required!'),
            'email.unique' => translate('Email must be unique!'),
            'phone.required' => translate('Phone is required!'),
            'phone.unique' => translate('Phone must be unique!'),
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
            'identity_image.*.mimes' => 'Identity image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'identity_image.*.max' => translate('Identity image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('delivery-man/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $identity_image = Helpers::upload('delivery-man/', APPLICATION_IMAGE_FORMAT, $img);
                $id_img_names[] = $identity_image;
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = $this->delivery_man;
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->branch_id = $request->branch_id;
        $dm->identity_image = $identity_image;
        $dm->image = $image_name;
        $dm->password = bcrypt($request->password);
        $dm->application_status= 'approved';
        $dm->save();

        Toastr::success(translate('Delivery-man added successfully!'));
        return redirect('admin/delivery-man/list');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $deliveryMan = $this->delivery_man->find($id);
        return view('admin-views.delivery-man.edit', compact('deliveryMan'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $deliveryMan = $this->delivery_man->find($request->id);
        $deliveryMan->status = $request->status;
        $deliveryMan->save();
        Toastr::success(translate('Delivery-man status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Request $request, $id): Redirector|RedirectResponse|Application
    {
        $check = $this->validateUploadedFile($request, ['image', 'identity_image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'f_name' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'password_confirmation' => 'required_with:password|same:password',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'identity_image' => 'sometimes|array',
            'identity_image.*' => 'image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'f_name.required' => 'First name is required!',
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
            'identity_image.*.mimes' => 'Identity image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'identity_image.*.max' => translate('Identity image size must be below ' . $this->maxImageSizeReadable),
        ]);

        $deliveryMan = $this->delivery_man->find($id);

        if ($deliveryMan['email'] != $request['email']) {
            $request->validate([
                'email' => 'required|unique:delivery_men',
            ]);
        }

        if ($deliveryMan['phone'] != $request['phone']) {
            $request->validate([
                'phone' => 'required|unique:delivery_men',
            ]);
        }

        if (!empty($request->file('image'))) {
            $image_name = Helpers::update('delivery-man/', $deliveryMan->image, APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = $deliveryMan['image'];
        }

        if (!empty($request->file('identity_image'))) {
            foreach (json_decode($deliveryMan['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                    Storage::disk('public')->delete('delivery-man/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                $identity_image = Helpers::upload('delivery-man/', APPLICATION_IMAGE_FORMAT, $img);
                $img_keeper[] = $identity_image;
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $deliveryMan['identity_image'];
        }
        $deliveryMan->f_name = $request->f_name;
        $deliveryMan->l_name = $request->l_name;
        $deliveryMan->email = $request->email;
        $deliveryMan->phone = $request->phone;
        $deliveryMan->identity_number = $request->identity_number;
        $deliveryMan->identity_type = $request->identity_type;
        $deliveryMan->branch_id = $request->branch_id;
        $deliveryMan->identity_image = $identity_image;
        $deliveryMan->image = $image_name;
        $deliveryMan->password = strlen($request->password) > 1 ? bcrypt($request->password) : $deliveryMan['password'];
        $deliveryMan->save();
        Toastr::success(translate('Delivery-man updated successfully'));
        return redirect('admin/delivery-man/list');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $deliveryMan = $this->delivery_man->find($request->id);
        if (Storage::disk('public')->exists('delivery-man/' . $deliveryMan['image'])) {
            Storage::disk('public')->delete('delivery-man/' . $deliveryMan['image']);
        }

        foreach (json_decode($deliveryMan['identity_image'], true) as $img) {
            if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                Storage::disk('public')->delete('delivery-man/' . $img);
            }
        }

        $deliveryMan->delete();
        Toastr::success(translate('Delivery-man removed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function pendingList(Request $request): Factory|View|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->delivery_man->with('branch')->where('application_status', 'pending');

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $deliveryMan = $query->latest()->paginate($perPage)->appends($queryParams);

        return view('admin-views.delivery-man.pending-list', compact('deliveryMan','search','perPage'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function deniedList(Request $request): Factory|View|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->delivery_man->with('branch')->where('application_status', 'denied');

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $deliveryMan = $query->latest()->paginate($perPage)->appends($queryParams);

        return view('admin-views.delivery-man.denied-list', compact('deliveryMan','search','perPage'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateApplication(Request $request): RedirectResponse
    {
        $deliveryMan = $this->delivery_man->findOrFail($request->id);
        $deliveryMan->application_status = $request->status;
        $deliveryMan->save();

        try{
            $emailServices = Helpers::get_business_settings('mail_config');
            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                Mail::to($deliveryMan->email)->send(new \App\Mail\DMSelfRegistration($request->status, $deliveryMan->f_name.' '.$deliveryMan->l_name));
            }

        }catch(\Exception $ex){
            info($ex);
        }

        Toastr::success(translate('application_status_updated_successfully'));
        return back();
    }
}
