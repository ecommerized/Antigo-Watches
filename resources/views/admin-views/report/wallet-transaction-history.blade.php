@extends('layouts.admin.app')

@section('title', translate('wallet_transaction_history'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/wallet-transaction.png')}}"
                     alt="{{ translate('wallet_transaction_history') }}">
                {{translate('wallet_transaction_history')}}
            </h2>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="" id="form-data" class="filter-form" method="GET">
                    <div class="row g-2 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('Start_Date') }}</label>
                                <input type="date" name="start_date" value="{{request('start_date')}}"
                                       class="form-control"
                                       placeholder="{{ translate('Select_Date') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('End_Date') }}</label>
                                <input type="date" name="end_date" value="{{request('end_date')}}" class="form-control"
                                       placeholder="{{ translate('Select_Date') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('Transaction_Type') }}
                                </label>
                                <select name="transaction_type" id="" class="form-control js-select2-custom mx-1"
                                        title="{{translate('Select_Transaction_Type')}}">
                                    <option
                                        value="all" {{ request('transaction_type') == 'all' ? "selected" : "" }}>{{translate('All_Transaction')}}</option>
                                    <option
                                        value="credit" {{request('transaction_type') == 'credit' ? "selected" : "" }}>{{translate("credit")}}</option>
                                    <option
                                        value="debit" {{request('transaction_type') == 'debit' ? "selected" : "" }}>{{translate("debit")}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('Customer') }}</label>
                                <select name="customer_id" id="" class="form-control js-select2-custom mx-1"
                                        title="{{translate('select_Customer')}}">
                                    <option value="all">{{translate('All_Customer')}}</option>
                                    @foreach($users as $user)
                                        <option
                                            value="{{$user['id']}}" {{ session()->get('customer_id') == $user['id'] ? 'selected' : '' }}>{{$user['f_name']. ' '. $user['l_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-end gap-3">
                        <button type="reset"
                                class="btn btn--secondary max-w-120 flex-grow-1">{{ translate('reset') }}</button>
                        <button type="submit"
                                class="btn btn-primary max-w-120 flex-grow-1">{{ translate('filter') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Wallet Transaction List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$walletTransactions->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by order ID')}}" aria-label="Search"
                                       value="{{$search}}" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-2 py-1 min-h-35">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div>
                            <button type="button"
                                    class="btn btn-outline-primary gap-1 d-flex font-weight-bold align-items-center min-h-35 py-1 fs-12 cmn-border"
                                    data-toggle="dropdown" aria-expanded="false">
                                <i class="tio-download-to mt-1"></i>{{ translate('Export') }}<i
                                    class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right w-auto">
                                <li>
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2"
                                       href="{{route('admin.report.export-wallet-transaction-history', [ 'start_date'=>request('start_date'), 'end_date'=>request('end_date'), 'transaction_type' => request('transaction_type') ?? 'all', 'customer_id'=>request('customer_id')??'all','search'=>request('search')])}}">
                                        <img width="14" src="{{asset('public/assets/admin/img/icons/excel.png')}}"
                                             alt="{{ translate('excel') }}">
                                        {{translate('excel')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th> {{ translate('SL') }}</th>
                        <th> {{ translate('Transaction_ID') }}</th>
                        <th> {{ translate('Customer_info') }}</th>
                        <th> {{ translate('date_&_time') }}</th>
                        <th class="text-center"> {{ translate('Transaction_Type') }}</th>
                        <th class="text-right"> {{ translate('Amount') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($walletTransactions as $key => $walletTransaction)
                        <tr>
                            <td>{{ $walletTransactions->firstItem() + $key }}</td>
                            <td>{{ $walletTransaction->transaction_id }}</td>
                            <td>
                                @if($walletTransaction->walletable)
                                    <div class="d-flex flex-column gap-1">
                                        <span>{{ $walletTransaction->walletable->f_name ?? '' }} {{ $walletTransaction->walletable->l_name ?? '' }}</span>
                                        <span
                                            class="opacity-lg">{{ $walletTransaction->walletable->phone ?? '' }}</span>
                                    </div>
                                @else
                                    <span class="badge badge-soft-danger">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span>{{ \Carbon\Carbon::parse($walletTransaction->created_at)->format('d M, Y') }}</span>
                                    <span
                                        class="opacity-lg">{{ \Carbon\Carbon::parse($walletTransaction->created_at)->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                {{ ucfirst($walletTransaction->direction) }}
                            </td>
                            <td class="text-right">
                                {{ \App\CentralLogics\Helpers::set_symbol($walletTransaction->amount) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="">
                {!! $walletTransactions->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($walletTransactions)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem"
                         src="{{asset('public/assets/admin//svg/illustrations/sorry.svg')}}"
                         alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>

    </div>
@endsection

@push('script_2')
    <script type="text/javascript" src="{{asset('public/assets/admin/js/filter-form-validation.js')}}"></script>

    <script>
        "use strict"

        $('#from_date,#to_date').change(function () {
            let from = $('#from_date').val();
            let to = $('#to_date').val();
            if (from != '' && to != '') {
                if (from > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error({{ translate('Invalid date range!') }}, Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });
    </script>

@endpush
