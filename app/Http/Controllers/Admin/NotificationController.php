<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Notification $notification
    ){
        $this->initUploadLimits();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    function index(Request $request): Factory|View|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->notification;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest()->paginate($perPage)->appends($queryParams);
        return view('admin-views.notification.index', compact('notifications', 'search','perPage'));
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
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'title.max' => 'Title must not greater than 100 character!',
            'description.max' => 'Description must not greater than 255 character!',
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('notification/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = null;
        }

        $notification = $this->notification;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $image_name;
        $notification->status = 1;
        $notification->save();

        try {
            $notification->type = 'general';
            Helpers::send_push_notif_to_topic($notification, 'general');
            Toastr::success(translate('Notification sent successfully!'));
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed!'));
        }

        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): Factory|View|Application
    {
        $notification = $this->notification->find($id);
        return view('admin-views.notification.edit', compact('notification'));
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
            'title' => 'required',
            'description' => 'required',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'title.required' => 'title is required!',
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        $notification = $this->notification->find($id);
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $request->has('image') ? Helpers::update('notification/', $notification->image, APPLICATION_IMAGE_FORMAT, $request->file('image')) : $notification->image;
        $notification->save();
        Toastr::success(translate('Notification updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $notification = $this->notification->find($request->id);
        $notification->status = $request->status;
        $notification->save();
        Toastr::success(translate('Notification status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $notification = $this->notification->find($request->id);
        if (Storage::disk('public')->exists('notification/' . $notification['image'])) {
            Storage::disk('public')->delete('notification/' . $notification['image']);
        }
        $notification->delete();
        Toastr::success(translate('Notification removed!'));
        return back();
    }
}
