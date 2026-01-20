@extends('layouts.admin.app')

@section('title', translate('Update banner'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/banner.png')}}" alt="{{ translate('banner') }}">
                {{translate('update_banner')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.banner.update',[$banner['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf @method('put')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="input-label">{{translate('title')}}</label>
                                <input type="text" name="title" value="{{$banner['title']}}" class="form-control"
                                       placeholder="{{ translate('New banner') }}" required>
                            </div>
                            <div class="mb-5">
                                <label class="input-label">{{translate('Banner')}} {{translate('type')}}<span
                                        class="input-label-secondary text-danger">*</span></label>
                                <select name="banner_type" class="form-control" id="banner_type">
                                    <option value="primary" {{ $banner['banner_type'] == 'primary' ? 'selected' : '' }}>{{translate('Primary Banner')}}</option>
                                    <option value="secondary" {{ $banner['banner_type'] == 'secondary' ? 'selected' : '' }}>{{translate('Secondary Banner')}}</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="input-label">{{translate('Redirection')}} {{translate('type')}}<span
                                        class="input-label-secondary">*</span></label>
                                <select name="item_type" class="form-control" id="redirection_type">
                                    <option value="product" {{$banner['product_id']==null?'':'selected'}}>{{translate('product')}}</option>
                                    <option value="category" {{$banner['category_id']==null?'':'selected'}}>{{translate('category')}}</option>
                                </select>
                            </div>

                            <div class="mb-5 type-product {{$banner['product_id'] == null ? 'd--none':'d-block'}}" id="type-product">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('product')}}
                                    <span class="input-label-secondary">*</span>
                                </label>
                                <select name="product_id" class="form-control js-select2-custom">
                                    @foreach($products as $product)
                                        <option
                                            value="{{$product['id']}}" {{$banner['product_id']==$product['id']?'selected':''}}>
                                            {{$product['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-5 type-category {{$banner['category_id']==null?'d--none':'d-block'}}" id="type-category">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('category')}}
                                    <span class="input-label-secondary">*</span>
                                </label>
                                <select name="category_id" class="form-control js-select2-custom">
                                    @foreach($categories as $category)
                                        <option value="{{$category['id']}}" {{$banner['category_id']==$category['id']?'selected':''}}>{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                        <div class="col-md-6">
                            <div class="form-group {{ $banner->banner_type != 'primary' ? 'd--none': '' }}" id="primary_banner">
                                <label class="mb-2">{{translate('Image')}}</label>
                                <div class="custom_upload_input max-h200px ratio-2">
                                    <input type="file" name="primary_image" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_meta_image_viewer"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img id="pre_meta_image_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ translate('img') }}">
                                    </div>
                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center existing-image-div">
                                        <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                            <img
                                                  src="{{Helpers::onErrorImage(
                                                            $banner['image'],
                                                            asset('storage/app/public/banner').'/' . $banner['image'],
                                                            asset('public/assets/admin/img/ratio/2_1.png') ,
                                                            'banner/')}}"
                                                  class="w-100" alt="{{ translate('banner') }}">
                                        </div>
                                    </div>
                                </div>

                                <p class="fs-16 mb-2 text-dark mt-2">
                                    <i class="tio-info-outlined cursor-pointer" data-toggle="tooltip"
                                       title="{{ translate('When do not have secondary banner than the primary banner ration will be 3:1') }}">
                                    </i>
                                    {{ translate('Images Ratio') }} 2:1
                                </p>
                                <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                            </div>

                            <div class="form-group {{ $banner->banner_type != 'secondary' ? 'd--none': '' }}" id="secondary_banner">
                                <label class="mb-2">{{translate('Image')}}</label>
                                <div class="custom_upload_input max-h200px ratio-1">
                                    <input type="file" name="secondary_image" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_meta_image_viewer"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img id="pre_meta_image_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ translate('img') }}">
                                    </div>
                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center existing-image-div">
                                        <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                            <img
                                                src="{{Helpers::onErrorImage(
                                                            $banner['image'],
                                                            asset('storage/app/public/banner').'/' . $banner['image'],
                                                            asset('public/assets/admin/img/ratio/1_1.png') ,
                                                            'banner/')}}"
                                                  class="w-100" alt="{{ translate('banner') }}">
                                        </div>
                                    </div>
                                </div>

                                <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Banner Images Ratio') }} 1:1</p>
                                <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                            </div>

                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary px-5">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary px-5">{{translate('update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/image-upload.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/banner.js') }}"></script>
@endpush
