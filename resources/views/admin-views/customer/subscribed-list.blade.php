@extends('layouts.admin.app')

@section('title', translate('Subscribed List'))

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/customer.png')}}" alt="{{ translate('customer') }}">
                {{translate('Subscribed_Customers')}}
            </h2>
            <span class="badge badge-soft-dark rounded-50 fs-14">{{$newsletters->total()}}</span>
        </div>

        <div class="card">

            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by id, name')}}" aria-label="Search"
                                       value="{{$search}}" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-2 py-1 min-h-35">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                                   href="{{ route('admin.customer.export-subscribed-emails', ['search' => Request::get('search')]) }}">
                                    <img width="14" src="{{asset('public/assets/admin/img/icons/excel.png')}}"
                                         alt="{{ translate('excel') }}">
                                    {{translate('excel')}}
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>
                                {{translate('SL')}}
                            </th>
                            <th class="text-center">{{translate('email')}}</th>
                            <th class="text-center">{{translate('subscribed_at')}}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($newsletters as $key=>$newsletter)
                        <tr>
                            <td>{{$newsletters->firstitem()+$key}}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <a class="text-dark" href="mailto:{{$newsletter['email']}}?subject={{translate('Mail from '). Helpers::get_business_settings('restaurant_name')}}">{{$newsletter['email']}}</a>
                                </div>
                            </td>
                            <td class="text-center">{{date('Y/m/d '.config('timeformat'), strtotime($newsletter->created_at))}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $newsletters->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($newsletters)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>

        <div class="modal fade" id="add-point-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" id="modal-content"></div>
            </div>
        </div>
    </div>
@endsection

