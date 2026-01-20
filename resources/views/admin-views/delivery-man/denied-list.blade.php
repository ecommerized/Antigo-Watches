@extends('layouts.admin.app')

@section('title', translate('New Joining Request'))

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="{{ translate('deliveryman') }}">
                {{translate('Denied Request')}}
            </h2>
            <span class="badge badge-soft-dark rounded-50 fs-14">{{$deliveryMan->total()}}</span>
        </div>
        <div class="mb-3">
            <ul class="nav nav-tabs border-0 mt-2">
                <li class="nav-item">
                    <a class="nav-link {{Request::is('admin/delivery-man/pending/list')?'active':''}}"  href="{{ route('admin.delivery-man.pending') }}">{{ translate('Pending Delivery Man') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{Request::is('admin/delivery-man/denied/list')?'active':''}}"  href="{{ route('admin.delivery-man.denied') }}">{{ translate('Denied Delivery Man') }}</a>
                </li>
            </ul>
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
                                       placeholder="{{translate('Search by name')}}" aria-label="Search"
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
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table text-dark">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('name')}}</th>
                            <th>{{translate('contact_Info')}}</th>
                            <th>{{translate('branch')}}</th>
                            <th>{{translate('Identity Type')}}</th>
                            <th>{{translate('Identity Number')}}</th>
                            <th class="text-center">{{translate('Identity Image')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($deliveryMan as $key=>$dm)
                        <tr>
                            <td>{{$deliveryMan->firstitem()+$key}}</td>
                            <td>
                                <div class="media gap-3 align-items-center">
                                    <div class="avatar rounded-circle">
                                        <img class="img-fit rounded-circle"
                                             src="{{$dm['image-fullpath']}}" alt="{{ translate('deliveryman') }}">
                                    </div>
                                    <div class="media-body">{{$dm['f_name'].' '.$dm['l_name']}}</div>
                                </div>
                            </td>
                            <td>
                                <div><a class="text-dark" href="tel:{{$dm['phone']}}">{{$dm['phone']}}</a></div>
                                <div><a class="text-dark" href="mailto:{{$dm['email']}}">{{$dm['email']}}</a></div>
                            </td>
                            <td>
                                @if($dm->branch_id == 0)
                                    <label class="badge badge-soft-primary">{{translate('All Branch')}}</label>
                                @else
                                    <label class="badge badge-soft-primary">{{$dm->branch?$dm->branch->name:'Branch deleted!'}}</label>
                                @endif
                            </td>
                            <td>{{ translate($dm->identity_type) }}</td>
                            <td>{{ $dm->identity_number }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-2" data-toggle="" data-placement="top" title="{{translate('click for bigger view')}}">
                                    @foreach($dm['identity_image_fullpath'] as $identification_image)
                                        <div class="mx-h80 overflow-hidden">
                                            <img class="cursor-pointer rounded img-fit p-2 w-100px max-h80px show-identification-image"
                                                 src="{{$identification_image}}"
                                                 data-image="{{$identification_image}}" alt="{{translate('image')}}">
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn btn-sm btn--primary btn-outline-primary action-btn route-alert"
                                       data-toggle="tooltip" data-placement="top" title="{{translate('Approve')}}"
                                       data-route="{{ route('admin.delivery-man.application', [$dm['id'], 'approved']) }}"
                                       data-message="{{ translate('you_want_to_deny_this_application') }}"
                                       href="javascript:"><i class="tio-done font-weight-bold"></i></a>
                                    @if ($dm->application_status != 'denied')
                                        <a class="btn btn-sm btn--danger btn-outline-danger action-btn route-alert" data-toggle="tooltip" data-placement="top" title="{{translate('Deny')}}"
                                           data-route="{{ route('admin.delivery-man.application', [$dm['id'], 'denied']) }}"
                                           data-message="{{ translate('you_want_to_deny_this_application') }}"
                                           href="javascript:"><i class="tio-clear"></i></a>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $deliveryMan->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($deliveryMan)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif

            <div class="modal fade bd-example-modal-lg" id="identification_image_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <div data-dismiss="modal">
                                <img alt="{{ translate('image') }}" src=""
                                     class="w-100" id="identification_image_element">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/deliveryman.js') }}"></script>

@endpush
