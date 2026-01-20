@extends('layouts.admin.app')

@section('title', translate('Add new delivery-man'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="{{ translate('deliveryman') }}">
                {{translate('Add_New_Deliveryman')}}
            </h2>
        </div>

        <form action="{{route('admin.delivery-man.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="tio-user"></i>
                        {{translate('General_Information')}}
                    </h5>
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{translate('first')}} {{translate('name')}}</label>
                                    <input type="text" name="f_name" class="form-control" placeholder="{{translate('first')}} {{translate('name')}}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{translate('last')}} {{translate('name')}}</label>
                                    <input type="text" name="l_name" class="form-control" placeholder="{{translate('last')}} {{translate('name')}}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{translate('phone')}}</label>
                                    <input type="text" name="phone" class="form-control" placeholder="{{ translate('Ex : 017********') }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{translate('branch')}}</label>
                                    <select name="branch_id" class="form-control">
                                        <option value="0">{{translate('all')}}</option>
                                        @foreach(\App\Models\Branch::all() as $branch)
                                            <option value="{{$branch['id']}}">{{$branch['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{translate('identity')}} {{translate('type')}}</label>
                                    <select name="identity_type" class="form-control">
                                        <option value="passport">{{translate('passport')}}</option>
                                        <option value="driving_license">{{translate('driving')}} {{translate('license')}}</option>
                                        <option value="nid">{{translate('nid')}}</option>
                                        <option value="restaurant_id">{{translate('store')}} {{translate('id')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{translate('identity')}} {{translate('number')}}</label>
                                    <input type="text" name="identity_number" class="form-control"
                                        placeholder="{{ translate('Ex : DH-23434-LS') }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="text-center mb-3">
                                        <img class="upload-img-view" id="viewer"
                                            src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt="{{ translate('delivery-man') }}"/>
                                    </div>

                                    <label class="mb-0">{{translate('deliveryman')}} {{translate('image')}}</label>
                                    <small class="text-danger">* ( {{translate('ratio')}} 1:1 )</small>
                                    <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}"
                                               required>
                                        <label class="custom-file-label" for="customFileEg1">{{translate('choose')}} {{translate('file')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-0">{{translate('identity')}} {{translate('image')}}</label>
                                    <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                    <div>
                                        <div class="row" id="coba"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="tio-user"></i>
                        {{translate('account_Information')}}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="input-label">{{translate('email')}}</label>
                                <input type="email" name="email" class="form-control" placeholder="{{ translate('Ex : ex@example.com') }}"
                                        required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="input-label">{{translate('password')}}</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password" class="js-toggle-password form-control input-field"
                                           placeholder="{{ translate('Password minimum 6 characters') }}" required
                                           data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                        }'>
                                    <div id="changePassTarget" class="input-group-append">
                                        <a class="input-group-text" href="javascript:">
                                            <i id="changePassIcon" class="tio-visible-outlined"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="input-label">{{translate('confirm_Password')}}</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password_confirmation" class="js-toggle-password form-control input-field"
                                           placeholder="{{ translate('Password minimum 6 characters') }}" required
                                           data-hs-toggle-password-options='{
                                        "target": "#changeConPassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changeConPassIcon"
                                        }'>
                                    <div id="changeConPassTarget" class="input-group-append">
                                        <a class="input-group-text" href="javascript:">
                                            <i id="changeConPassIcon" class="tio-visible-outlined"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin/js/deliveryman.js')}}"></script>
    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>

    <script type="text/javascript">
        "use strict"
        let maxSizeReadable = "{{ readableUploadMaxFileSize('image') }}";
        let maxFileSize = 2 * 1024 * 1024; // default 2MB

        if (maxSizeReadable.toLowerCase().includes('mb')) {
            maxFileSize = parseFloat(maxSizeReadable) * 1024 * 1024;
        } else if (maxSizeReadable.toLowerCase().includes('kb')) {
            maxFileSize = parseFloat(maxSizeReadable) * 1024;
        }

        function setAcceptForAllInputs() {
            const allowedExtensions = ".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}";
            $('#coba input[type=file]').each(function() {
                $(this).attr('accept', allowedExtensions);
            });
        }
        setAcceptForAllInputs();

        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '160px',
                groupClassName: 'col-6 col-sm-4 col-md-6',
                maxFileSize: maxFileSize,
                placeholderImage: {
                    image: '{{asset('public/assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                allowedExt:        'png|jpg|jpeg|gif|webp',
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {
                    setAcceptForAllInputs();
                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{ translate("Please only input png or jpg type file") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{ translate("File size too big") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
@endpush
