@extends('layouts.admin.app')

@section('title', translate('Add new product'))

@push('css_or_js')
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/product.png')}}"
                     alt="{{ translate('product') }}">
                {{translate('add_new_product')}}
            </h2>
        </div>


        <div class="row">
            <div class="col-12">
                <form action="{{ route('admin.product.store') }}" method="post" id="product_form"
                      enctype="multipart/form-data">
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = 'bn')
                    @if($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-4 max-content">

                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#"
                                       id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach

                        </ul>
                        @foreach(json_decode($language) as $lang)
                            <div class="card mb-3 card-body {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                 id="{{$lang}}-form">
                                <div class="form-group">
                                    <label class="input-label" for="{{$lang}}_name">
                                        {{translate('name')}}({{strtoupper($lang)}})
                                        @if($lang == 'en')
                                            <span class="input-label-secondary text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="text"  name="name[]"
                                           id="{{$lang}}_name" class="form-control"
                                           placeholder="{{ translate('New Product') }}"
                                           >
                                    @if($lang == 'en')
                                        <span class="error-text" data-error="name.0"></span>
                                    @endif
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                <div class="form-group pt-4">
                                    <label class="input-label"
                                           for="{{$lang}}_description">{{translate('short')}} {{translate('description')}}
                                        ({{strtoupper($lang)}})</label>
                                    <div id="{{$lang}}_editor"></div>
                                    <textarea name="description[]" style="display:none"
                                              id="{{$lang}}_hiddenArea"></textarea>
                                    @if($lang == 'en')
                                        <span class="error-text" data-error="description.0"></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card p-4" id="{{$default_lang}}-form">
                            <div class="form-group">
                                <label class="input-label">{{translate('name')}} (EN)
                                    <span class="input-label-secondary text-danger">*</span>
                                </label>
                                <input type="text" name="name[]" class="form-control"
                                       placeholder="{{ translate('new_product') }}" >
                                <span class="error-text" data-error="name.0"></span>
                            </div>
                            <input type="hidden" name="lang[]" value="en">
                            <div class="form-group pt-4">
                                <label class="input-label">{{translate('short')}} {{translate('description')}}
                                    (EN)</label>
                                <div id="editor"></div>
                                <textarea name="description[]" style="display:none" id="hiddenArea"></textarea>
                                <span class="error-text" data-error="description.0"></span>
                            </div>
                        </div>
                    @endif

                    <div id="from_part_2">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('price')}}
                                                    <span class="input-label-secondary text-danger">*</span></label>
                                            <input type="number" min="1" max="100000000" step="0.01" value="1"
                                                   name="price"
                                                   class="form-control"
                                                   placeholder="{{ translate('Ex : 100') }}"
                                                   onkeydown="return !['e','E','+','-'].includes(event.key)"
                                                   oninput="
                                                   if (this.value < 1) this.value = 1;
                                                   if (this.value.includes('.')) {this.value = this.value.split('.').map((part, index) => index === 1 ? part.slice(0, 2) : part).join('.');}
                                                   "
                                            >
                                            <span class="error-text" data-error="price"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('unit')}}<span class="input-label-secondary text-danger">*</span></label>
                                            <select name="unit" class="form-control js-select2-custom">
                                                <option value="kg">{{translate('kg')}}</option>
                                                <option value="gm">{{translate('gm')}}</option>
                                                <option value="ltr">{{translate('ltr')}}</option>
                                                <option value="pc">{{translate('pc')}}</option>
                                            </select>
                                        </div>
                                        <span class="error-text" data-error="unit"></span>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('tax')}}<span class="input-label-secondary text-danger">*</span></label>
                                            <input type="number" min="0" value="0" step="0.01" max="100000" name="tax"
                                                   class="form-control"
                                                   placeholder="{{ translate('Ex : 7') }}"
                                                   onkeydown="return !['e','E','+','-'].includes(event.key)"
                                                   oninput="
                                                   if (this.value < 0) this.value = 0;
                                                   if (this.value.includes('.')) {this.value = this.value.split('.').map((part, index) => index === 1 ? part.slice(0, 2) : part).join('.');}
                                                   "
                                            >
                                            <span class="error-text" data-error="tax"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('tax')}} {{translate('type')}}<span class="input-label-secondary text-danger">*</span></label>
                                            <select name="tax_type" class="form-control js-select2-custom">
                                                <option value="percent">{{translate('percent')}}</option>
                                                <option value="amount">{{translate('amount')}}</option>
                                            </select>
                                        </div>
                                        <span class="error-text" data-error="tax_type"></span>
                                    </div>

                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('discount')}}<span class="input-label-secondary text-danger">*</span></label>
                                            <input type="number" min="0" max="100000" value="0" step="0.01"
                                                   name="discount" class="form-control"
                                                   placeholder="{{ translate('Ex : 100') }}"
                                                   onkeydown="return !['e','E','+','-'].includes(event.key)"
                                                   oninput="
                                                   if (this.value < 0) this.value = 0;
                                                   if (this.value.includes('.')) {this.value = this.value.split('.').map((part, index) => index === 1 ? part.slice(0, 2) : part).join('.');}
                                                   "
                                            >
                                            <span class="error-text" data-error="discount"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('discount')}} {{translate('type')}}<span class="input-label-secondary text-danger">*</span></label>
                                            <select name="discount_type" class="form-control js-select2-custom">
                                                <option value="percent">{{translate('percent')}}</option>
                                                <option value="amount">{{translate('amount')}}</option>
                                            </select>
                                        </div>
                                        <span class="error-text" data-error="discount_type"></span>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('stock')}}<span class="input-label-secondary text-danger">*</span></label>
                                            <input type="number" min="1" max="100000000" value="1" name="total_stock"
                                                   class="form-control"
                                                   placeholder="{{ translate('Ex : 100') }}"
                                                   onkeydown="return !['e','E','+','-','.'].includes(event.key)"
                                                   oninput="
                                                   if (this.value < 1) this.value = 1;
                                                   "
                                            >
                                            <span class="error-text" data-error="total_stock"></span>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlSelect1">{{translate('category')}}<span class="input-label-secondary text-danger">*</span></label>
                                            <select name="category_id" class="form-control js-select2-custom"
                                                    onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                                <option value="">---{{translate('select category')}}---</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                @endforeach
                                            </select>
                                            <span class="error-text" data-error="category_id"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlSelect1">{{translate('sub_category')}}<span
                                                        class="input-label-secondary"></span></label>
                                            <select name="sub_category_id" id="sub-categories"
                                                    class="form-control js-select2-custom"
                                                    onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">
                                            </select>
                                            <span class="error-text" data-error="sub_category_id"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="input-label">
                                                {{translate('select_attributes')}}
                                                <span class="input-label-secondary"></span>
                                            </label>
                                            <select name="attribute_id[]" id="choice_attributes"
                                                    class="form-control js-select2-custom"
                                                    multiple="multiple">
                                                @foreach(\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                                    <option value="{{$attribute['id']}}">{{$attribute['name']}}</option>
                                                @endforeach
                                            </select>
                                            <span class="error-text" data-error="attribute_id"></span>
                                        </div>
                                        <div class="customer_choice_options mb-4" id="customer_choice_options"></div>
                                        <div class="variant_combination mb-4" id="variant_combination"></div>
                                        <div>
                                            <div class="mb-2">
                                                <label class="text-capitalize">{{translate('product_image')}}</label>
                                                <small class="text-danger"> * ( {{translate('ratio')}} 1:1 )</small>
                                            </div>
                                            <div class="row" id="coba"></div>
                                            <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                            <span class="error-text justify-content-start" data-error="images"></span>
                                        </div>
                                        <div class="d-flex justify-content-end gap-3">
                                            <button type="reset"
                                                    class="btn btn-secondary">{{translate('reset')}}</button>
                                            <button type="submit"
                                                    class="btn btn-primary">{{translate('submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>
    <script src="{{ asset('public/assets/admin/js/quill-editor.js') }}"></script>

    <script>
        "use strict";

        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $("#from_part_2").removeClass('d-none');
            } else {
                $("#from_part_2").addClass('d-none');
            }
        })

        $(function () {
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

            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 4,
                rowHeight: '215px',
                groupClassName: 'col-auto',
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
                    toastr.error('{{ translate("Please only input png, jpg, jpeg, gif, webp type file") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error(`"${file.name}" exceeds ${maxSizeReadable} limit!`, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="Choice Title" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="Enter choice values" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.product.variant-combination')}}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    $('#variant_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        @if($language)
        @foreach(json_decode($language) as $lang)
        var en_quill = new Quill('#{{$lang}}_editor', {
            theme: 'snow'
        });
        @endforeach
        @else
        var bn_quill = new Quill('#editor', {
            theme: 'snow'
        });
        @endif

        submitByAjax('#product_form', {
            hasEditors: true,
            languages: @json(json_decode($language) ?? []),
            successMessage: '{{ translate("product uploaded successfully!") }}',
            redirectUrl: '{{ route('admin.product.list') }}'
        });

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {
                $('input[name="total_stock"]').attr("readonly", true);
                $('input[name="total_stock"]').val(total_qty);
            } else {
                $('input[name="total_stock"]').attr("readonly", false);
            }
        }
    </script>
@endpush
