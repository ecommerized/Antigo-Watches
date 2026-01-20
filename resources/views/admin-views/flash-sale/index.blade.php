@extends('layouts.admin.app')

@section('title', translate('Flash sale'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="16" src="{{asset('public/assets/admin/img/icons/flash-sale.png')}}" alt="{{ translate('flash-sale') }}">
                {{translate('Flash sale')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.flash-sale.store')}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">{{translate('Title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ translate('Ex : LUX') }}" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">{{ translate('Start Date')}}</label>
                                <input type="datetime-local" name="start_date" id="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">{{translate('End Date')}}</label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary px-5">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary px-5">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Flash Sale List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$flashSales->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by title')}}" aria-label="Search"
                                       value="{{$search}}" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-2 py-1 min-h-35">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('Title')}}</th>
                            <th>{{translate('Duration')}}</th>
                            <th>{{translate('status')}}</th>
                            <th>{{translate('Active Products')}}</th>
                            <th>{{translate('Publish')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($flashSales as $key => $flash_sale)
                        <tr>
                            <td> {{$flashSales->firstitem()+$key}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-25">
                                    {{$flash_sale['title']}}
                                </span>
                            </td>
                            <td>{{date('Y-m-d H:i A',strtotime($flash_sale['start_date']))}} - {{date('Y-m-d H:i A',strtotime($flash_sale['end_date']))}}</td>
                            <td>
                                @if(\Carbon\Carbon::parse($flash_sale['end_date'])->endOfDay()->isPast())
                                    <span class="badge badge-soft-danger">{{ translate('expired')}} </span>
                                @else
                                    <span class="badge badge-soft-success"> {{ translate('active')}} </span>
                                @endif
                            </td>
                            <td>{{  $flash_sale->products_count }}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input route-alert"
                                           data-route="{{ route('admin.flash-sale.status', [$flash_sale->id, $flash_sale->status ? 0 : 1]) }}"
                                           data-message="{{ $flash_sale->status? translate('you_want_to_disable_this'): translate('you_want_to_active_this') }}"
                                        {{ $flash_sale->status ? 'checked' : '' }} >
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-soft-primary btn-sm py-1" href="{{route('admin.flash-sale.add-product', [$flash_sale['id']])}}">
                                        <i class="tio tio-add"></i> {{ translate('Add Product') }}
                                    </a>
                                    <a class="btn btn-outline-info square-btn" href="{{route('admin.flash-sale.edit',[$flash_sale['id']])}}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="deal-{{$flash_sale['id']}}"
                                       data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.flash-sale.delete',[$flash_sale['id']])}}"
                                      method="post" id="deal-{{$flash_sale['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $flashSales->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
             @if(count($flashSales)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/image-upload.js') }}"></script>

    <script>
        "use strict"

        $('#start_date, #end_date').change(function () {
            let from = $('#start_date').val();
            let to = $('#end_date').val();
            if (from != '' && to != '') {
                if (from > to) {
                    $('#start_date').val('');
                    $('#end_date').val('');
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });

    </script>

@endpush
