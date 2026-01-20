@extends('layouts.admin.app')

@section('title', translate('Add new sub category'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/brand-setup.png')}}" alt="{{ translate('image') }}">
                {{translate('sub_category_Setup')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.category.store')}}" method="post" id="category_form">
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = 'en')
                    @if($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-4 max-content">
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="row g-2">
                            <div class="col-sm-6 col-lg-4">
                                @foreach(json_decode($language) as $lang)
                                    <div class="form-group m-0 {{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{translate('sub_category')}} {{translate('name')}} ({{strtoupper($lang)}})
                                            @if($lang == 'en')
                                                <span class="input-label-secondary text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="text" name="name[]" class="form-control" maxlength="255" placeholder="{{ translate('New Sub Category') }}">
                                        @if($lang == 'en')
                                            <span class="error-text" data-error="name.0"></span>
                                        @endif
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                                @else
                                <div class="row g-2 align-items-end">
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="form-group m-0 lang_form" id="{{$default_lang}}-form">
                                            <label class="input-label" for="exampleFormControlInput1">
                                                {{translate('sub_category')}} {{translate('name')}}({{strtoupper($lang)}})
                                                <span class="input-label-secondary text-danger">*</span>
                                            </label>
                                            <input type="text" name="name[]" class="form-control" placeholder="{{ translate('New Sub Category') }}" >
                                        </div>
                                        <span class="error-text" data-error="name.0"></span>
                                        <input type="hidden" name="lang[]" value="{{$default_lang}}">
                                        @endif
                                        <input name="position" value="1" class="d-none">
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="form-group m-0">
                                            <label class="input-label"
                                                    for="exampleFormControlSelect1">{{translate('main')}} {{translate('category')}}
                                                <span class="input-label-secondary text-danger">*</span></label>
                                            <select id="exampleFormControlSelect1" name="parent_id" class="form-control" required>
                                                @foreach(\App\Models\Category::where(['position'=>0])->get() as $category)
                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-4 d-flex justify-content-end">
                                        <div class="d-flex justify-content-end align-items-center gap-3 mt-4">
                                            <button type="reset" class="btn min-w-120 btn-secondary">{{translate('reset')}}</button>
                                            <button type="submit" class="btn min-w-120 btn-primary">{{translate('submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>

                <div class="card mt-3">
                    <div class="p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <h6 class="m-0">{{translate('Sub Category List ')}}</h6>
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
                        <table class="table table-border table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('main')}} {{translate('category')}}</th>
                                <th>{{translate('sub_category')}}</th>
                                <th>{{translate('status')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>

                            </thead>

                            <tbody id="set-rows">
                            @foreach($categories as $key => $category)
                                <tr>
                                    <td>{{$categories->firstItem()+$key}}</td>
                                    <td class="text-title">{{$category->parent['name']}}</td>
                                    <td>{{$category['name']}}</td>
                                    <td>
                                        @if($category['status']==1)
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input change-status"
                                                       {{$category['status']==1? 'checked' : ''}} id="{{$category['id']}}"
                                                       data-route="{{route('admin.category.status',[$category['id'],0])}}">
                                                <span class="switcher_control"></span>
                                            </label>
                                        @else
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input change-status"
                                                       {{$category['status']==1? 'checked' : ''}} id="{{$category['id']}}"
                                                       data-route="{{route('admin.category.status',[$category['id'],1])}}">
                                                <span class="switcher_control"></span>
                                            </label>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline--primary square-btn"
                                               href="{{route('admin.category.edit',[$category['id']])}}"><i
                                                    class="tio tio-edit"></i></a>
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
                            <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('Image') }}">
                            <p class="mb-0">{{ translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script_2')
    <script src="{{ asset('public/assets/admin/js/category.js') }}"></script>
    <script>
        "use strict";
        submitByAjax('#category_form', {
            hasEditors: false,
            languages: @json(json_decode($language) ?? []),
            successMessage: '{{ translate("Sub category added successfully!") }}',
            redirectUrl: '{{ route('admin.category.add-sub-category') }}'
        });
    </script>
@endpush
