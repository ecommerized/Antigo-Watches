@extends('layouts.admin.app')

@section('title', translate('Add new category'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/brand-setup.png')}}" alt="{{ translate('image') }}">
                {{translate('category_Setup')}}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{route('admin.category.store')}}" method="post" enctype="multipart/form-data" id="category_form">
                    @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = 'en')
                    @if ($language)
                    @php($default_lang = json_decode($language)[0])
                    <ul class="nav nav-tabs mb-4 max-content">
                        @foreach (json_decode($language) as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}" href="#"
                                    id="{{ $lang }}-link">{{ Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="row">
                        <div class="col-12">
                            @foreach (json_decode($language) as $lang)
                                <div class="form-group {{ $lang != $default_lang ? 'd-none' : '' }} lang_form"  id="{{ $lang }}-form">
                                    <label class="input-label">
                                        {{ translate('name') }} ({{ strtoupper($lang) }})
                                        @if($lang == 'en')
                                            <span class="input-label-secondary text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{ translate('New Category') }}" maxlength="255"
                                             >
                                    @if($lang == 'en')
                                        <span class="error-text" data-error="name.0"></span>
                                    @endif
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            @endforeach
                            @else
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group lang_form" id="{{ $default_lang }}-form">
                                        <label class="input-label">
                                            {{ translate('name') }} ({{ strtoupper($lang) }})
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input type="text" name="name[]" class="form-control" maxlength="255"
                                                placeholder="{{ translate('New Category') }}" >
                                        <span class="error-text" data-error="name.0"></span>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                                    @endif
                                    <input name="position" value="0" class="d-none">
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="mb-2">{{translate('Image')}}</label>
                                        <div class="custom_upload_input ratio-1 max-w-200">
                                            <input type="file" name="image" class="custom-upload-input-file meta-img h-100" id="" data-imgpreview="pre_meta_image_viewer"
                                                   accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                                   data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d-none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_meta_image_viewer" class="h-auto aspect-1 bg-white ratio-1" src="img" onerror="this.classList.add('d-none')">
                                            </div>
                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                    <h3 class="text-muted">{{ translate('Drag & Drop here') }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="error-text justify-content-start" data-error="image"></span>
                                        <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Image Ratio') }} 1:1</p>
                                        <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>

                                    </div>
                                </div>
                                <div class="col-md-8">

                                    <div class="form-group">
                                        <label class="mb-2">{{translate('Banner Image')}}</label>
                                        <div class="custom_upload_input max-h200px ratio-8">
                                            <input type="file" name="banner_image" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_meta_image_viewer"
                                                   accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                                   data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d-none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_meta_image_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')">
                                            </div>
                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                                    <h3 class="text-muted">{{ translate('Drag & Drop here') }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="error-text" data-error="banner_image"></span>
                                        <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Banner Images Ratio') }} 8:1</p>
                                        <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="reset" class="btn btn-secondary px-5">{{translate('reset')}}</button>
                                <button type="submit" class="btn btn-primary px-5">{{translate('submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Category List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$categories->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
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
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('Category_Image')}}</th>
                            <th>{{translate('name')}}</th>
                            <th>{{translate('Is Featured')}} ? <i class="tio-info-outined cursor-pointer" data-toggle="tooltip" title="{{ translate('If enable, the category will show in featured category') }}"></i></th>
                            <th>{{translate('status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($categories as $key=>$category)
                        <tr>
                            <td>{{$categories->firstItem()+$key}}</td>
                            <td>
                                <div class="avatar-lg rounded border">
                                    <img class="img-fit rounded"
                                         src="{{$category['image_fullpath']}}"
                                         alt="{{ translate('image') }}">
                                </div>
                            </td>
                            <td>{{$category['name']}}</td>
                            <td>
                                <label class="on-off-toggle">
                                    <input class="on-off-toggle__input change-status" type="checkbox"
                                        {{$category['is_featured']==1? 'checked' : ''}}
                                        data-route="{{route('admin.category.featured',[$category['id'], $category->is_featured == 1 ? 0: 1])}}">
                                    <span class="on-off-toggle__slider"></span>
                                </label>
                            </td>
                            <td>
                                @if($category['status']==1)
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" {{$category['status']==1? 'checked' : ''}}
                                                id="{{$category['id']}}"
                                               data-route="{{route('admin.category.status',[$category['id'],0])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @else
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" {{$category['status']==1? 'checked' : ''}}
                                                id="{{$category['id']}}"
                                               data-route="{{route('admin.category.status',[$category['id'],1])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn" href="{{route('admin.category.edit',[$category['id']])}}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="category-{{$category['id']}}"
                                       data-message="{{translate('Want to delete this ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.category.delete',[$category['id']])}}"
                                        method="post" id="category-{{$category['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $categories->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($categories)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/image-upload.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/category.js') }}"></script>
    <script>
        "use strict";
        submitByAjax('#category_form', {
            hasEditors: false,
            languages: @json(json_decode($language) ?? []),
            successMessage: '{{ translate("Category added successfully!") }}',
            redirectUrl: '{{ route('admin.category.add') }}'
        });
    </script>
@endpush
