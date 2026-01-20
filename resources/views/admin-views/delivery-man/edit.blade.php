@extends('layouts.admin.app')

@section('title', translate('Update delivery-man'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="">
                {{translate('update_Deliveryman')}}
            </h2>
        </div>

        <form action="{{route('admin.delivery-man.update',[$deliveryMan['id']])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="tio-user"></i>{{translate('general_Information')}}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('first')}} {{translate('name')}}</label>
                                <input type="text" value="{{$deliveryMan['f_name']}}" name="f_name"
                                       class="form-control" placeholder="{{ translate('New delivery-man') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('last')}} {{translate('name')}}</label>
                                <input type="text" value="{{$deliveryMan['l_name']}}" name="l_name"
                                       class="form-control" placeholder="{{ translate('Last Name') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('phone')}}</label>
                                <input type="text" name="phone" value="{{$deliveryMan['phone']}}" class="form-control"
                                       placeholder="{{ translate('Ex : 017********') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('branch')}}</label>
                                <select name="branch_id" class="form-control">
                                    <option value="0" {{$deliveryMan['branch_id']==0?'selected':''}}>{{translate('all')}}</option>
                                    @foreach(\App\Models\Branch::all() as $branch)
                                        <option value="{{$branch['id']}}" {{$deliveryMan['branch_id']==$branch['id']?'selected':''}}>{{$branch['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('identity')}} {{translate('type')}}</label>
                                <select name="identity_type" class="form-control">
                                    <option
                                        value="passport" {{$deliveryMan['identity_type']=='passport'?'selected':''}}>
                                        {{translate('passport')}}
                                    </option>
                                    <option
                                        value="driving_license" {{$deliveryMan['identity_type']=='driving_license'?'selected':''}}>
                                        {{translate('driving')}} {{translate('license')}}
                                    </option>
                                    <option value="nid" {{$deliveryMan['identity_type']=='nid'?'selected':''}}>{{translate('nid')}}
                                    </option>
                                    <option
                                        value="restaurant_id" {{$deliveryMan['identity_type']=='restaurant_id'?'selected':''}}>
                                        {{translate('store')}} {{translate('id')}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('identity')}} {{translate('number')}}</label>
                                <input type="text" name="identity_number" value="{{$deliveryMan['identity_number']}}"
                                       class="form-control"
                                       placeholder="{{ translate('Ex : DH-23434-LS') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="mb-3 align-content-center">
                                    <img class="upload-img-view" id="viewer"
                                         src="{{$deliveryMan['image-fullpath']}}" alt="delivery-man image"/>
                                </div>
                                <label class="mb-0">{{translate('deliveryman')}} {{translate('image')}}</label>
                                <small class="text-danger">* ( {{translate('ratio')}} 1:1 )</small>
                                <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
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

                            <div class="row">
                                @foreach($deliveryMan['identity_image_fullpath'] as $img)
                                    <div class="col-6 col-sm-4 col-md-6">
                                        <div><img class="mx-w100 max-h150px" src="{{$img}}" alt="{{ translate('image') }}"></div>
                                    </div>
                                @endforeach
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
                                <input type="email" value="{{$deliveryMan['email']}}" name="email" class="form-control"
                                       placeholder="{{ translate('Ex : ex@example.com') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="input-label">{{translate('password')}}</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password" class="js-toggle-password form-control input-field"
                                           placeholder="{{ translate('Password minimum 6 characters') }}"
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
                                           placeholder="{{ translate('Password minimum 6 characters') }}"
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
                rowHeight: '120px',
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
