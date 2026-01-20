@extends('layouts.admin.app')

@section('title', translate('Add new notification'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/notification.png')}}" alt="{{translate('notification')}}">
                {{translate('notification')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.notification.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ translate('New notification') }}" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('description')}}</label>
                                <textarea name="description" class="form-control" required maxlength="255"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <label class="mb-0">{{translate('Image')}}</label>
                                    <small class="text-danger">( {{ translate('ratio') }} 1:1 )</small>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <div class="upload-file">
                                        <input type="file" name="image" id="customFileEg1"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}"
                                               class="upload-file__input" required>
                                        <div class="upload-file__img">
                                            <img width="150" id="viewer" src="{{asset('public/assets/admin/img/icons/upload_img.png')}}" alt="{{translate('notification')}}">
                                        </div>
                                    </div>
                                </div>
                                <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                class="btn btn-primary demo-form-submit">{{translate('send_notification')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Notification List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$notifications->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by title / description')}}" aria-label="Search"
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
                        <th>{{translate('image')}}</th>
                        <th>{{translate('title')}}</th>
                        <th>{{translate('description')}}</th>
                        <th>{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($notifications as $key=>$notification)
                        <tr>
                            <td>{{$notifications->firstitem()+$key}}</td>
                            <td>
                                @if($notification['image']!=null)
                                    <div class="avatar-lg border rounded">
                                        <img class="img-fit rounded"
                                            src="{{$notification['image_fullpath']}}"
                                        alt="{{translate('image')}}">
                                    </div>
                                @else
                                    <label class="badge badge-soft-warning">{{translate('No')}} {{translate('image')}}</label>
                                @endif
                            </td>
                            <td>
                                {{substr($notification['title'],0,25)}} {{strlen($notification['title'])>25?'...':''}}
                            </td>
                            <td>
                                {{substr($notification['description'],0,25)}} {{strlen($notification['description'])>25?'...':''}}
                            </td>
                            <td>
                                @if($notification['status']==1)
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" checked id="{{$notification['id']}}"
                                               data-route="{{route('admin.notification.status',[$notification['id'],0])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @else
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status"  id="{{$notification['id']}}"
                                               data-route="{{route('admin.notification.status',[$notification['id'],1])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('admin.notification.edit',[$notification['id']])}}"><i class="tio tio-edit"></i></a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="notification-{{$notification['id']}}"
                                       data-message="{{translate('Want to delete this notification ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.notification.delete',[$notification['id']])}}"
                                    method="post" id="notification-{{$notification['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $notifications->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($notifications)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/image-upload.js') }}"></script>
@endpush
