@extends('layouts.admin.app')

@section('title', translate('Add new banner'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="24" src="{{asset('public/assets/admin/img/icons/banner.png')}}" alt="{{ translate('banner') }}">
                {{translate('add_new_banner')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.banner.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-5">
                                <label class="input-label">{{translate('title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ translate('New banner') }}" required maxlength="255">
                            </div>
                            <div class="mb-5">
                                <label class="input-label">{{translate('Banner')}} {{translate('type')}}<span
                                        class="input-label-secondary text-danger">*</span></label>
                                <select name="banner_type" class="form-control" id="banner_type">
                                    <option value="primary">{{translate('Primary Banner')}}</option>
                                    <option value="secondary">{{translate('Secondary Banner')}}</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="input-label">{{translate('Redirection')}} {{translate('type')}}<span
                                        class="input-label-secondary text-danger">*</span></label>
                                <select name="item_type" class="form-control" id="redirection_type">
                                    <option value="product">{{translate('product')}}</option>
                                    <option value="category">{{translate('category')}}</option>
                                </select>
                            </div>
                            <div class="mb-5 type-product" id="type-product">
                                <label class="input-label">{{translate('product')}}
                                    <span class="input-label-secondary text-danger">*</span>
                                </label>
                                <select name="product_id" class="form-control js-select2-custom">
                                    @foreach($products as $product)
                                        <option value="{{$product['id']}}">{{$product['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-5 d--none type-category" id="type-category">
                                <label class="input-label">
                                    {{translate('category')}}
                                    <span class="input-label-secondary text-danger">*</span>
                                </label>
                                <select name="category_id" class="form-control js-select2-custom">
                                    @foreach($categories as $category)
                                        <option value="{{$category['id']}}">{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group" id="primary_banner">
                                <label class="mb-2">{{translate('Image')}}</label>
                                <div class="custom_upload_input max-h200px ratio-2">
                                    <input type="file" name="primary_image" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_meta_image_viewer"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn" style="display: none">
                                                <i class="tio-delete"></i>
                                            </span>

                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img id="pre_meta_image_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ translate('image') }}">
                                    </div>
                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                        <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                            <h3 class="text-muted">{{ translate('Drag & Drop here') }}</h3>
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

                            <div class="form-group d--none" id="secondary_banner">
                                <label class="mb-2">{{translate('Image')}}</label>
                                <div class="custom_upload_input max-h200px ratio-1">
                                    <input type="file" name="secondary_image" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_meta_image_viewer"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn" style="display: none">
                                                <i class="tio-delete"></i>
                                            </span>

                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img id="pre_meta_image_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ 'image' }}">
                                    </div>
                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                        <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                            <h3 class="text-muted">{{ translate('Drag & Drop here') }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Images Ratio') }} 1:1</p>
                                <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
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

        <div class="card mt-3">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Banner List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$banners->total()}}</span>
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
                            <th>{{translate('banner_image')}}</th>
                            <th>{{translate('title')}}</th>
                            <th>{{translate('type')}}</th>
                            <th>{{translate('status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($banners as $key=>$banner)
                        <tr>
                            <td>{{$banners->firstitem()+$key}}</td>
                            <td>
                                <div class="banner-img-wrap rounded border">
                                    <img class="img-fit" src="{{$banner['image_fullpath']}}"
                                         alt="{{ translate('banner') }}">
                                </div>
                            </td>
                            <td>{{$banner['title']}}</td>
                            <td>{{$banner['banner_type']}}</td>
                            <td>
                                @if($banner['status']==1)
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" checked
                                               data-route="{{route('admin.banner.status',[$banner['id'],0])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @else
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status"
                                               data-route="{{route('admin.banner.status',[$banner['id'],1])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('admin.banner.edit',[$banner['id']])}}"><i class="tio tio-edit"></i></a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="banner-{{$banner['id']}}"
                                       data-message="{{translate('Want to delete this banner ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.banner.delete',[$banner['id']])}}"
                                        method="post" id="banner-{{$banner['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $banners->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($banners)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/image-upload.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/banner.js') }}"></script>

@endpush
