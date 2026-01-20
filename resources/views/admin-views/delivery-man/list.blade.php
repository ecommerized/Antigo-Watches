@extends('layouts.admin.app')

@section('title', translate('Deliveryman List'))

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="{{ translate('deliveryman') }}">
                {{translate('deliveryman_List')}}
            </h2>
            <span class="badge badge-soft-dark rounded-50 fs-14">{{$deliveryMan->total()}}</span>
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
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{route('admin.delivery-man.add')}}" class="btn btn-primary gap-1 d-flex font-weight-bold align-items-center min-h-35 py-1 fs-12 cmn-border">
                            <i class="tio-add-circle"></i>
                            {{translate('add_deliveryman')}}
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-border table-thead-bordered table-nowrap table-align-middle card-table text-dark">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('name')}}</th>
                            <th>{{translate('contact_Info')}}</th>
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
                                             src="{{$dm['image-fullpath']}}" alt="{{translate('image')}}">
                                    </div>
                                    <div class="media-body">{{$dm['f_name'].' '.$dm['l_name']}}</div>
                                </div>
                            </td>
                            <td>
                                <div><a class="text-dark" href="tel:{{$dm['phone']}}">{{$dm['phone']}}</a></div>
                                <div><a class="text-dark" href="mailto:{{$dm['email']}}">{{$dm['email']}}</a></div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-10">
                                    <a class="btn btn-outline-primary square-btn"
                                        href="{{route('admin.delivery-man.edit',[$dm['id']])}}"><i class="tio-edit"></i></a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="delivery-man-{{$dm['id']}}"
                                       data-message="{{translate('Want to delete this deliveryman ?')}}">
                                        <i class="tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.delivery-man.delete',[$dm['id']])}}"
                                        method="post" id="delivery-man-{{$dm['id']}}">
                                    @csrf @method('delete')
                                </form>
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
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

