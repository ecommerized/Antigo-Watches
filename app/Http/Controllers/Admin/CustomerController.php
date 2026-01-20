<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Models\Order;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class CustomerController extends Controller
{
    public function __construct(
        private Newsletter $newsletter,
        private Order $order,
        private User $user
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function customerList(Request $request): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->user;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        $customers = $query->with(['orders'])->latest()->paginate($perPage)->appends($queryParams);
        return view('admin-views.customer.list', compact('customers', 'search','perPage'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function view($id, Request $request): View|Factory|RedirectResponse|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];
        $search = $request->query('search');
        $customer = $this->user->find($id);
        if ($search) {
            $queryParams['search'] = $search;
        }
        if (isset($customer)) {
            $orders = $this->order->latest()->where(['user_id' => $id])
                ->when($search, function ($query) use ($search) {
                    $key = explode(' ', $search);
                    foreach ($key as $value) {
                        $query->where('id', 'like', "%$value%");
                    }
                })
                ->paginate($perPage)->appends($queryParams);
            return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search', 'perPage'));
        }
        Toastr::error(translate('Customer not found!'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function subscribedEmails(Request $request): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');

        // Start query
        $query = $this->newsletter;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('email', 'like', "%{$search}%");
            });
        }

        $newsletters = $query->latest()->paginate($perPage)->appends($queryParams);
        return view('admin-views.customer.subscribed-list', compact('newsletters', 'search', 'perPage'));
    }

    public function exportSubscribedEmails(Request $request)
    {
        $search = $request->query('search');

        // Start query
        $query = $this->newsletter;

        // Apply search filter
        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('email', 'like', "%{$search}%");
            });
        }
        $newsletters = $query->latest()->get();

        $data = [];
        foreach ($newsletters as $key => $newsletter) {
            $data[] = [
                'SL' => ++$key,
                'Email' => $newsletter->email,
                'Subscribe At' => date('d M Y h:m A', strtotime($newsletter['created_at'])),
            ];
        }
        return (new FastExcel($data))->download('subscribe-email.xlsx');
    }
}
