@extends('layouts.admin.app')

@section('title', translate('branch_list'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/branch.png')}}" alt="{{ translate('branch') }}">
                {{translate('branch_list')}}
            </h2>
        </div>

        <div class="card">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Branch List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$branches->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by branch Name')}}" aria-label="Search"
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
                        <th>{{translate('branch_name')}}</th>
                        <th>{{translate('branch_type')}}</th>
                        <th>{{translate('Contact_info')}}</th>
                        <th>{{translate('Delivery Charge Type')}}</th>
                        <th>{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($branches as $key=>$branch)
                        <tr>
                            <td>{{$branches->firstItem()+$key}}</td>
                            <td>
                                <div class="media gap-3 align-items-center">
                                    <div class="avatar">
                                        <img class="img-fit"
                                             src="{{$branch['image_fullpath']}}" alt="{{ translate('branch') }}">
                                    </div>
                                    <div class="media-body">
                                        {{$branch['name']}}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($branch['id']==1)
                                    <div class="text-capitalize">{{translate('main_branch')}}</div>
                                @else
                                    <div class="text-capitalize">{{translate('sub_branch')}}</div>
                                @endif
                            </td>
                            <td>
                                <a class="text-dark" href="mailto:{{$branch['email']}}?subject={{translate('Mail from '). \App\Models\BusinessSetting::where(['key' => 'restaurant_name'])->first()->value}}">{{$branch['email']}}</a>
                            </td>
                            <td>
                                <span class="badge badge-soft-success"> {{ $branch?->delivery_charge_setup?->delivery_charge_type }} </span>
                            </td>
                            <td>
                                @if($branch['status']==1)
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" checked
                                               data-route="{{route('admin.branch.status',[$branch['id'],0])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @else
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status"
                                               data-route="{{route('admin.branch.status',[$branch['id'],1])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-primary square-btn" target="_blank"
                                       href="{{ route('admin.business-settings.delivery-fee-setup') }}">
                                        <i class="tio-settings"></i>
                                    </a>
                                    <a class="btn btn-outline-info square-btn"
                                       href="{{route('admin.branch.edit',[$branch['id']])}}"><i class="tio tio-edit"></i></a>
                                    @if($branch['id']!=1)
                                        <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                           data-id="branch-{{$branch['id']}}"
                                           data-message="{{translate('Want to delete this branch ?')}}">
                                            <i class="tio tio-delete"></i>
                                        </a>
                                    @endif
                                </div>
                                <form action="{{route('admin.branch.delete',[$branch['id']])}}"
                                      method="post" id="branch-{{$branch['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $branches->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($branches)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

