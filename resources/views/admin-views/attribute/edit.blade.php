@extends('layouts.admin.app')

@section('title', translate('Update Attribute'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/attribute.png')}}" alt="{{ translate('attribute') }}">
                {{translate('attribute_update')}}
            </h2>
        </div>


        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.attribute.update',[$attribute['id']])}}" method="post" id="attribute_form">
                    @csrf
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
                        <div class="row">
                            <div class="col-12">
                                @foreach(json_decode($language) as $lang)
                                    <?php
                                    if(count($attribute['translations'])){
                                        $translate = [];
                                        foreach($attribute['translations'] as $t)
                                        {
                                            if($t->locale == $lang && $t->key=="name"){
                                                $translate[$lang]['name'] = $t->value;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                         id="{{$lang}}-form">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{translate('name')}}({{strtoupper($lang)}})
                                            @if($lang == 'en')
                                                <span class="input-label-secondary text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="text" name="name[]" class="form-control"
                                               oninvalid="document.getElementById('en-link').click()" maxlength="255"
                                               value="{{$lang==$default_lang?$attribute['name']:($translate[$lang]['name']??'')}}"
                                               placeholder="{{ translate('New Attribute') }}" >
                                        @if($lang == 'en')
                                            <span class="error-text" data-error="name.0"></span>
                                        @endif
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('name')}} ({{strtoupper($lang)}})<span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="name[]" class="form-control" value="{{ $attribute['name'] }}" placeholder="{{ translate('New Attribute') }}" >
                                    <span class="error-text" data-error="name.0"></span>
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        'use strict'

        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$default_lang}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });

        submitByAjax('#attribute_form', {
            hasEditors: false,
            languages: @json(json_decode($language) ?? []),
            successMessage: '{{ translate("Attribute updated successfully!") }}',
            redirectUrl: '{{ route('admin.attribute.add-new') }}'
        });
    </script>
@endpush
