@extends('layouts.admin.app')

@section('title', translate('Update category'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/brand-setup.png')}}"
                     alt="{{ translate('image') }}">
                @if($category->parent_id == 0)
                    {{translate('category_update')}}
                @else
                    {{translate('sub_category_update')}}
                @endif
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{route('admin.category.update',[$category['id']])}}" method="post"
                              enctype="multipart/form-data" id="category_form">
                            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')
                            @if($language)
                                @php($default_lang = json_decode($language)[0])
                                <ul class="nav nav-tabs mb-4 max-content">
                                    @foreach(json_decode($language) as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}"
                                               href="#"
                                               id="{{$lang}}-link">{{Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        @foreach(json_decode($language) as $lang)
                                                <?php
                                                if (count($category['translations'])) {
                                                    $translate = [];
                                                    foreach ($category['translations'] as $t) {
                                                        if ($t->locale == $lang && $t->key == "name") {
                                                            $translate[$lang]['name'] = $t->value;
                                                        }
                                                    }
                                                }
                                                ?>
                                            <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                                 id="{{$lang}}-form">
                                                <label class="input-label"
                                                       for="exampleFormControlInput1">
                                                    {{translate('name')}}({{strtoupper($lang)}})
                                                    @if($lang == 'en')
                                                        <span class="input-label-secondary text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" name="name[]" maxlength="255"
                                                       value="{{$lang==$default_lang?$category['name']:($translate[$lang]['name']??'')}}"
                                                       class="form-control"
                                                       oninvalid="document.getElementById('en-link').click()"
                                                       placeholder="{{ translate('New Category') }}"
                                                >
                                                @if($lang == 'en')
                                                    <span class="error-text" data-error="name.0"></span>
                                                @endif
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{$lang}}">
                                        @endforeach
                                        @else
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group lang_form" id="{{$default_lang}}-form">
                                                        <label class="input-label"
                                                               for="exampleFormControlInput1">
                                                            {{translate('name')}}({{strtoupper($lang)}})
                                                            <span class="input-label-secondary text-danger">*</span>
                                                        </label>
                                                        <input type="text" name="name[]" value="{{$category['name']}}"
                                                               class="form-control"
                                                               oninvalid="document.getElementById('en-link').click()"
                                                               placeholder="{{ translate('New Category') }}">
                                                        <span class="error-text" data-error="name.0"></span>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="{{$default_lang}}">
                                                    @endif
                                                    <input name="position" value="0" class="d-none">
                                                </div>
                                                @if($category->parent_id == 0)
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="mb-2">{{translate('Image')}}</label>
                                                            <div class="custom_upload_input ratio-1 max-w-200">
                                                                <input type="file" name="image"
                                                                       class="custom-upload-input-file meta-img h-100"
                                                                       id="" data-imgpreview="pre_meta_image_viewer"
                                                                       accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                                                       data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                                                <div
                                                                    class="img_area_with_preview position-absolute z-index-2">
                                                                    <img id="pre_meta_image_viewer"
                                                                         class="h-auto aspect-1 bg-white ratio-1"
                                                                         src=""
                                                                         onerror="this.classList.add('d-none')" alt="">
                                                                </div>
                                                                <div
                                                                    class="img_area_with_preview position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                    <div
                                                                        class="d-flex flex-column justify-content-center align-items-center">
                                                                        <img src="{{$category['image_fullpath']}}"
                                                                             class="w-100" alt="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="error-text justify-content-start"
                                                                  data-error="image"></span>
                                                            <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Images Ratio') }}
                                                                1:1</p>
                                                            <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label class="mb-2">{{translate('Banner Image')}}</label>
                                                            <div class="custom_upload_input max-h200px ratio-8">
                                                                <input type="file" name="banner_image"
                                                                       class="custom-upload-input-file meta-img" id=""
                                                                       data-imgpreview="pre_meta_image_viewer"
                                                                       accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                                                       data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                                                <div
                                                                    class="img_area_with_preview position-absolute z-index-2">
                                                                    <img id="pre_meta_image_viewer"
                                                                         class="aspect-1 bg-white" src="img"
                                                                         onerror="this.classList.add('d-none')">
                                                                </div>
                                                                <div
                                                                    class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center existing-image-div">
                                                                    <div
                                                                        class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                                                        <img
                                                                            src="{{$category['banner_image_fullpath']}}"
                                                                            class="w-100">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="error-text" data-error="banner_image"></span>
                                                            <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Banner Images Ratio') }}
                                                                8:1</p>
                                                            <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>

                                                        </div>

                                                    </div>
                                                @else
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="input-label"
                                                                   for="exampleFormControlSelect1">
                                                                {{translate('main')}} {{translate('category')}}
                                                                <span class="input-label-secondary text-danger">*</span>
                                                            </label>
                                                            <select id="exampleFormControlSelect1" name="parent_id"
                                                                    class="form-control" required>
                                                                @foreach(\App\Models\Category::where(['position'=>0])->get() as $main_category)
                                                                    <option
                                                                        value="{{$main_category['id']}}" {{ $main_category['id'] == $category['parent_id'] ? 'selected' : ''}}>{{$main_category['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                            <div class="d-flex justify-content-end gap-3">
                                                <button type="reset"
                                                        class="btn btn-secondary">{{translate('reset')}}</button>
                                                <button type="submit"
                                                        class="btn btn-primary">{{translate('update')}}</button>
                                            </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
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
            successMessage: '{{ $category->parent_id == 0 ? translate("Category updated successfully!") : translate("Sub category updated successfully!") }}',
            redirectUrl: '{{ $category->parent_id == 0 ? route('admin.category.add') : route('admin.category.add-sub-category') }}'
        });
    </script>
@endpush
