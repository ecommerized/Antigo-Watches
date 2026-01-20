@extends('layouts.admin.app')

@section('title', translate('Update Notification'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/notification.png')}}" alt="{{ translate('notification') }}">
                {{translate('update_notification')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.notification.update',[$notification['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                <input type="text" value="{{$notification['title']}}" name="title" class="form-control" placeholder="{{ translate('New notification') }}" required>
                            </div>
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('description')}}</label>
                                <textarea name="description" class="form-control" required>{{$notification['description']}}</textarea>
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
                                        <input type="file" id="customFileEg1" name="image"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}"
                                               class="upload-file__input">
                                        <div class="upload-file__img">
                                            <img width="150" id="viewer"
                                                 src="{{$notification['image_fullpath']}}" alt="{{ translate('notification') }}">
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
                                class="btn btn-primary demo-form-submit">{{translate('submit')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/image-upload.js') }}"></script>
@endpush
