@extends('layouts.admin.app')

@section('title', translate('Order List'))

@section('content')
    <h1>Conponent</h1>
    <a class="btn btn-outline--info square-btn" href="">
        <i class="tio-edit"></i>
    </a>
    <a class="btn btn-outline--primary square-btn" href="">
        <i class="tio-visible"></i>
    </a>
    <a class="btn btn-outline-primary square-btn" href="">
        <i class="tio-visible"></i>
    </a>
    <a class="btn btn-outline-info square-btn" target="_blank" href="">
        <i class="tio-download"></i>
    </a>
    <a class="btn btn-outline--primary square-btn" href=""><i class="tio-edit"></i></a>

    <a class="btn btn-outline--info square-btn offcanvas-trigger"
       data-target="#offcanvas__order_edit" href="">
        <i class="tio-edit"></i>
    </a>
    <h1>Check Validation</h1>
    <form action="" class="fnd-validation">
        <div class="card mb-4">
            <div class="card-body">
                <ul class="nav nav-tabs mb-4 max-content">
                    <li class="nav-item">
                        <a class="nav-link lang_link active" href="#0" id="en-link">English(EN)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#0" id="ar-link">Arabic - العربية(AR)</a>
                    </li>
                </ul>
                <div class="row g-3 align-items-xl-center mb-3">
                    <div class="col-lg-8 col-md-7">
                        <div class="d-flex flex-column gap-24px">
                            <div class="lang_form" id="default-form">
                                <div class="d-flex flex-column gap-24px">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-title d-flex align-items-center gap-1">
                                            User name <span class="text-danger">*</span>
                                            <i class="tio-info fs-14 text-gray-info" data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('Content Need') }}">
                                            </i>
                                        </label>
                                        <input type="text" name="name" class="form-control" placeholder="User Name"
                                               required="">
                                        <span class="typing-error">Please enter your first name.</span>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="input-label text-title d-flex align-items-center gap-1">
                                            Email Address <span class="text-danger">*</span>
                                            <i class="tio-info fs-14 text-gray-info" data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('Content Need') }}">
                                            </i>
                                        </label>
                                        <input type="text" name="email" class="form-control"
                                               placeholder="Type Your Mail" required="">
                                        <span class="typing-error">Please enter a valid email</span>
                                    </div>
                                    <div class="form-group counting-character-item mb-0">
                                        <div
                                            class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                                            <label
                                                class="input-label text-title d-flex align-items-center gap-1 fs-14 text-title">
                                                Description
                                                <i class="tio-info fs-14 text-gray-info" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="{{ translate('Content Need') }}">
                                                </i>
                                            </label>
                                            <label class="switcher without-validation">
                                                <input type="checkbox" class="switcher_input" checked="" id="">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                        <textarea type="text" name="bio" rows="3" class="form-control"
                                                  maxlength="300" placeholder="Write Description"
                                                  required=""></textarea>
                                        <span class="counting-character text-right mt-1 d-block">0/300</span>
                                        <span class="typing-error">Typing something</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="form-group mb-0">
                            <label
                                class="text-center d-block text-dark mb-3 text-center text-title font-weight-bold">{{translate('Upload  Image')}}</label>
                            <div class="upload--img-wrap position-relative">
                                <div class="btn-group-uplod bg-white rounded-pill p-1 position-absolute">
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                             <span class="btn edit-reupload btn--info btn-sm square-btn">
                                                <i class="tio-edit"></i>
                                            </span>
                                        <span class="btn delete-img btn-danger btn-sm square-btn">
                                                <i class="tio-clear"></i>
                                            </span>
                                    </div>
                                </div>
                                <div class="custom_upload_input bg-color-common ratio-1 max-w-200 mx-auto">
                                    <input type="file" name="image" class="custom-upload-input-file meta-img h-100"
                                           id="" data-imgpreview="pre_meta_image_viewer"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <span class="delete_file_input d-none btn btn-outline-danger btn-sm square-btn">
                                            <i class="tio-delete"></i>
                                        </span>
                                    <div class="img_area_with_preview position-absolute z-index-2 p-0">
                                        <img id="pre_meta_image_viewer" class="h-auto aspect-1 bg-white ratio-1"
                                             src="img" onerror="this.classList.add('d-none')">
                                    </div>
                                    <div
                                        class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                        <div
                                            class="d-flex flex-column gap-1 justify-content-center align-items-center">
                                            <i class="tio-photo-camera text-primary fs-24"></i>
                                            <div class="text-title2 fs-10">{{ translate('Add image') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="typing-error justify-content-center fs-10 mt-1">File size is large</span>
                            <span class="fs-10 justify-content-center d-block text-center mt-3">JPG, JPEG, PNG Image size : Max 5 MB <strong>(1:1)</strong></span>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group without-validation m-0 country__select">
                            <label for="country_selector"
                                   class="input-label text-title">{{translate('Phone Number')}} <span
                                    class="text-danger">*</span></label>
                            </label>
                            <input type="tel" placeholder="Ex: +9XXX-XXX-XXXX"
                                   class="phone form-control bg-white w-100 overflow-hidden" name="country_select"
                                   id="country_selector" required>
                            <span class="typing-error">Select Counry Code or number</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0">
                            <label class="input-label text-title">Phone <span class="text-danger">*</span></label>
                            <input type="phone" name="phone" class="form-control" placeholder="EX : +09853834"
                                   required="">
                            <span class="typing-error">Please enter a valid number</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0">
                            <label class="input-label text-title">Selection</label>
                            <select name="brand_selection" class="custom-select">
                                <option value="" selected disabled>Select Brand</option>
                                <option value="all">All Brand</option>
                                <option value="1">examle 01</option>
                                <option value="2">examle 02</option>
                                <option value="3">examle 03</option>
                            </select>
                            <span class="typing-error">Select Brand</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0 without-validation">
                            <label class="input-label text-title">Selection</label>
                            <div class="d-flex align-items-center gap-0 border rounded">
                                <input type="text" name="time-select" placeholder="Select from dropdown"
                                       class="from-control px-3 w-100 border-0 rounded-0">
                                <select name="brand_selection"
                                        class="custom-select border-0 rounded-0 w-auto bg-light">
                                    <option value="" selected disabled>Hour</option>
                                    <option value="all">Minutes</option>
                                    <option value="1">Secound</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="without-validation">
                            <label class="input-label text-title">Digital Payment <span
                                    class="text-danger">*</span></label>
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span class="mb-0">Status</span>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" checked="" id="">
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0 without-validation">
                            <label class="input-label text-title">Order Notification Type <span
                                    class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-4 align-items-center form-control border">
                                <div class="custom-radio">
                                    <input type="radio" id="firebase" name="status" value="0" class="top-02">
                                    <label for="firebase" class="text-title2"> Firebase</label>
                                </div>
                                <div class="custom-radio">
                                    <input type="radio" id="manual" name="status" value="0" class="top-02">
                                    <label for="manual" class="text-title2"> Manual</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0">
                            <label class="input-label text-title d-flex align-items-center gap-1">
                                Password
                                <i class="tio-info fs-14 text-gray-info" data-toggle="tooltip" data-placement="top"
                                   title="{{ translate('Content Need') }}">
                                </i>
                            </label>
                            <div class="position-relative rounded border toggle-password_custom">
                                <input type="password" class="toggle-password_input form-control border-0 pr-5"
                                       name="password" placeholder="Password" required>
                                <div class="input-group-change z-1 mr-3 position-absolute right-0 top-0 mt-2">
                                    <a class="eye-on text-title2" href="javascript:">
                                        <i class="tio-visible-outlined"></i>
                                    </a>
                                    <a class="eye-off text-title2" href="javascript:">
                                        <i class="tio-hidden-outlined"></i>
                                    </a>
                                </div>
                            </div>
                            <span class="typing-error">Type Strong Password</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0">
                            <label class="input-label text-title d-flex align-items-center gap-1">
                                Confirm Password
                                <i class="tio-info fs-14 text-gray-info" data-toggle="tooltip" data-placement="top"
                                   title="{{ translate('Content Need') }}">
                                </i>
                            </label>
                            <div class="position-relative rounded border toggle-password_custom">
                                <input type="password" class="toggle-password_input form-control border-0 pr-5"
                                       name="confirm_password" placeholder="Password" required>
                                <div class="input-group-change z-1 mr-3 position-absolute right-0 top-0 mt-2">
                                    <a class="eye-on text-title2" href="javascript:">
                                        <i class="tio-visible-outlined"></i>
                                    </a>
                                    <a class="eye-off text-title2" href="javascript:">
                                        <i class="tio-hidden-outlined"></i>
                                    </a>
                                </div>
                            </div>
                            <span class="typing-error">Passwords do not match</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0 without-validation">
                            <label class="input-label text-title d-flex align-items-center gap-1">
                                Date
                                <i class="tio-info fs-14 text-gray-info" data-toggle="tooltip" data-placement="top"
                                   title="{{ translate('Content Need') }}">
                                </i>
                            </label>
                            <div class="position-relative">
                                <input type="text"
                                       name="date_range"
                                       id="date_range"
                                       class="js-flatpickr form-control flatpickr-custom pe-5"
                                       placeholder="{{ translate('Start date - End date') }}">

                                <span
                                    class="position-absolute top-0 mt-2 right-0 translate-middle-y mr-3 text-muted">
                                        <i class="tio-calendar"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group mb-0 without-validation">
                            <label class="input-label d-flex align-items-center gap-1">
                                Time
                                <i class="tio-info text-muted" data-toggle="tooltip" data-placement="top" title=""
                                   data-original-title="Content Need">
                                </i>
                            </label>
                            <label class="input-time w-100">
                                <input type="time" name="dates" id="" value="" class="form-control" placeholder="">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end gap-3 mt-2">
                    <button type="reset" class="btn btn--reset min-w-120 min-h-45px">{{translate('Reset')}}</button>
                    <button type="submit"
                            class="btn btn--primary min-w-120 min-h-45px">{{translate('Submit')}}</button>
                </div>
            </div>
        </div>
    </form>

    <div class="modal cmn__quick-modal fade" id="quick-view_custom" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="--modal-mxwidth: 650px; max-width: 650px !important;">
            <div class="modal-content" id="quick-view-modal">
                <div class="modal-header p-2">
                    <h4 class="modal-title product-title"></h4>
                    <button class="close call-when-done" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-1">
                    <div class="media flex-wrap gap-3">
                        <div class="box-120 rounded border">
                            <img class="img-fit rounded"
                                 src="http://localhost/Hexacom-backend-admin/storage/app/public/product/2023-06-22-6493ed179b3b7.png"
                                 data-zoom="http://localhost/Hexacom-backend-admin/storage/app/public/product/2023-06-22-6493ed179b3b7.png"
                                 alt="Product image">
                            <div class="cz-image-zoom-pane"></div>
                        </div>

                        <div class="details media-body">
                            <h5 class="product-name mb-1"><a href="#" class="h3 mb-0 product-title fs-16">Plain Half
                                    Sleeve T-shirt
                                    for Men</a>
                            </h5>
                            <div class="mb-1">
                            <span class="h3 font-weight-normal fs-14 text-price text-decoration-line-through">
                                300.00$
                            </span>
                                <span class="h2 fs-20 text-title">
                                250.00$
                            </span>
                            </div>
                            <div class="mb-0 text-price fs-14">
                            <span class="stock-badge">Stock Qty : <strong><span
                                        class="total-stock text-dark">100</span></strong></span>
                            </div>
                            <!-- Description -->
                            <div class="row pt-lg-3 pt-2">
                                <div class="col-12">
                                    <!-- <div class="mb-3">
                                                                    <h2 class="fs-14 mb-1">Description</h2>
                                        <article>
                                            <p class="d-block text-dark fs-12 m-0" id="description-27">
                                            <span id="description-text-27">
                                                Plain Half Sleeve T-shirt for MenType: t-shirtMaterial: Synthetic &amp; CottonFor MenBest quality productClothing Length: Regular
                                            </span>
                                                                                </p>
                                        </article>
                                    </div> -->
                                    <div class="border rounded p-3">
                                        <input type="hidden" name="id" value="27">
                                        <h3 class="mb-2 pt-0 fs-14">Color</h3>
                                        <div class="d-flex gap-3 flex-wrap mb-3">
                                            <input class="btn-check" type="radio" id="choice_4-Red" name="choice_4"
                                                   value="Red"
                                                   checked="" autocomplete="off">
                                            <label
                                                class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                                for="choice_4-Red">Red</label>
                                            <input class="btn-check" type="radio" id="choice_4- Blue" name="choice_4"
                                                   value=" Blue"
                                                   autocomplete="off">
                                            <label
                                                class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                                for="choice_4- Blue"> Blue</label>
                                            <input class="btn-check" type="radio" id="choice_4- Black" name="choice_4"
                                                   value=" Black" autocomplete="off">
                                            <label
                                                class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                                for="choice_4- Black"> Black</label>
                                        </div>
                                        <h3 class="mb-2 pt-0 fs-14">Size</h3>
                                        <div class="d-flex gap-3 flex-wrap mb-3">
                                            <input class="btn-check" type="radio" id="choice_1-XL" name="choice_1"
                                                   value="XL"
                                                   checked="" autocomplete="off">
                                            <label
                                                class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                                for="choice_1-XL">XL</label>
                                            <input class="btn-check" type="radio" id="choice_1- L" name="choice_1"
                                                   value=" L"
                                                   autocomplete="off">
                                            <label
                                                class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                                for="choice_1- L"> L</label>
                                            <input class="btn-check" type="radio" id="choice_1- XXL" name="choice_1"
                                                   value=" XXL"
                                                   autocomplete="off">
                                            <label
                                                class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                                for="choice_1- XXL"> XXL</label>
                                            <input class="btn-check" type="radio" id="choice_1- M" name="choice_1"
                                                   value=" M"
                                                   autocomplete="off">
                                            <label
                                                class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                                for="choice_1- M"> M</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer w-100 border-0 bg-white">
                    <div class="w-100">
                        <div class="row ">
                            <div class="col-12">
                                <div class="row no-gutters text-dark d-flex align-items-center" id="chosen_price_div">
                                    <div class="col">
                                        <div class="product-description-label h5 font-weight-light mb-0">Total Amount:
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="product-price text-right text-primary h2 font-weight-bold mb-0">
                                            <strong id="chosen_price">200.00</strong> $
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="row no-gutters mt-3 text-dark flex-row-reverse d-flex align-items-center justify-content-center"
                                    id="chosen_price_div">
                                    <div class="col text-right">
                                        <div class="max-w-270 ml-auto">
                                            <button
                                                class="btn d-flex align-items-center gap-2 justify-content-center btn-primary add-to-shopping-cart font-weight-bold w-em-100 ml-auto"
                                                type="button">
                                                <i class="tio-shopping-cart"></i>
                                                <div class="d-em-block d-none text-nowrap">
                                                    Add to cart
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col d-flex justify-content-center">
                                        <div class="product-quantity d-flex align-items-center">
                                            <div class="d-flex justify-content-center align-items-center gap-3"
                                                 id="quantity_div">
                                                <button class="btn btn-number py-1 px-2 text-dark" type="button"
                                                        data-type="minus"
                                                        data-field="quantity" disabled="disabled">
                                                    <i class="tio-remove font-weight-bold"></i>
                                                </button>
                                                <input type="text" name="quantity" id="quantity"
                                                       class="form-control h-30px input-number text-center px-2 cart-qty-field min-w-35 w-25"
                                                       placeholder="1" value="1" min="1">
                                                <div class="tooltip-wrapper position-relative d-inline-block">
                                                    <button class="btn btn-number py-1 px-2 text-dark" type="button"
                                                            data-type="plus" data-field="quantity">
                                                        <i class="tio-add font-weight-bold"></i>
                                                    </button>

                                                    <!-- Tooltip -->
                                                    <div class="custom-tooltip">
                                                        <div class="tooltip-body">
                                                            <div class="tooltip-icon">⚠️</div>
                                                            <div class="tooltip-content">
                                                                <div class="h5 font-weight-light">Warning</div>
                                                                <div class="fs-12">
                                                                    There isn’t enough quantity on stock.<br>Only <span
                                                                        class="total-stock">100</span> is available.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span class="tooltip-close"
                                                              onclick="this.parentElement.style.display='none'">×</span>
                                                        <div class="tooltip-arrow"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Edit -->
    <div id="offcanvas__order_edit" class="custom-offcanvas d-flex flex-column justify-content-between"
         style="--offcanvas-width: 750px">
        <div>
            <div
                class="custom-offcanvas-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div class="bg-white px-3 py-3 d-flex justify-content-between w-100">
                    <div>
                        <h2 class="mb-1">{{ translate('Edit Products') }}</h2>
                        <div class="d-flex flex-wrap align-items-center gapy_30px">
                            <h3 class="page-header-title d-flex align-items-center gap-2">
                                <span class="font--max-sm fs-14">Order #100065</span>
                                <span class="badge badge-soft-info font-regular m-0">Pending</span>
                            </h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-14 font-regular d-block text-dark">Order Placed :</span>
                                <span class="fs-14 font-weight-bolder d-block text-dark"> 05 Oct 2024 06:31 pm</span>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            class="btn-close w-35 h-35 min-w-35 rounded-circle d-flex align-items-center justify-content-center bg--secondary position-relative offcanvas-close border-0 fs-18"
                            aria-label="Close">&times;
                    </button>
                </div>
            </div>
            <div class="custom-offcanvas-body p-20">
                <form class="mb-20 position-relative edit-search-form">
                    <div class="form-control bg-white d-flex align-items-center gap-2">
                        <i class="tio-search"></i>
                        <input id="" type="search" name="search"
                               class="h-100 fs-12 bg-transparent w-100 border-0 rounded-0"
                               value="" placeholder="Search by food name" autocomplete="off">
                        <!--- After Search -->
                        <div class="search-wrap-manage w-100">
                            <div class="search-items-wrap p-sm-3 p-2 rounded bg-white d-flex flex-column gap-2">
                                <div class="text-center gap-2 py-5 px-3 bg-light border rounded">
                                    <p class="text-title2 m-0">No Items found</p>
                                </div>
                                <div
                                    class="search-item active d-flex align-items-sm-center gap-2 p-2 border rounded cursor-pointer"
                                    data-toggle="modal" data-target="#quick-view_custom">
                                    <div class="list-items-media cursor-pointer">
                                        <div class="thumb position-relative rounded overflow-hidden w-65px h-65px">
                                            <img width="55" height="55"
                                                 src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                 alt="image"
                                                 class="rounded">
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex w-100 flex-sm-nowrap flex-wrap align-items-center justify-content-between search-items-body">
                                        <div class="cont d-flex flex-column gap-0">
                                            <p class="fs-14 text-dark mb-0 max-w-440 line--limit-1">B39 Bluetooth 5.0
                                                Headphone Ear Shape Wireless Headset phone Ear Shape </p>
                                            <div class="fs-12">Stock Qty : <span class="text-dark">10</span></div>
                                        </div>
                                        <div class="text-sm-right cont d-flex flex-column gap-0">
                                            <div class="text-dark fs-12 text-title text-nowrap">Unit Price</div>
                                            <div class="d-flex align-items-center gap-1">
                                                <h6 class="m-0 font-semibold text-dark fs-14">129.40$</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="search-item d-flex align-items-sm-center gap-2 p-2 border rounded cursor-pointer"
                                    data-toggle="modal" data-target="#quick-view_custom">
                                    <div class="list-items-media cursor-pointer">
                                        <div class="thumb position-relative rounded overflow-hidden w-65px h-65px">
                                            <img width="55" height="55"
                                                 src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                 alt="image"
                                                 class="rounded">
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex w-100 flex-sm-nowrap flex-wrap align-items-center justify-content-between search-items-body">
                                        <div class="cont d-flex flex-column gap-0">
                                            <p class="fs-14 text-dark mb-0 max-w-440 line--limit-1">B39 Bluetooth 5.0
                                                Headphone Ear Shape Wireless Headset phone Ear Shape </p>
                                            <div class="fs-12">Stock Qty : <span class="text-dark">10</span></div>
                                        </div>
                                        <div class="text-sm-right cont d-flex flex-column gap-0">
                                            <div class="text-dark fs-12 text-title text-nowrap">Unit Price</div>
                                            <div class="d-flex align-items-center gap-1">
                                                <h6 class="m-0 font-semibold text-dark fs-14">129.40$</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="search-item unavailable d-flex align-items-sm-center gap-2 p-2 border rounded cursor-pointer"
                                    data-toggle="modal" data-target="#quick-view_custom">
                                    <div class="list-items-media cursor-pointer">
                                        <div
                                            class="thumb d-center position-relative rounded overflow-hidden w-65px h-65px">
                                            <img width="55" height="55"
                                                 src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                 alt="image"
                                                 class="rounded">
                                            <div class="text-white fs-10 font-medium position-absolute unavail">Stock
                                                Out
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex w-100 flex-sm-nowrap flex-wrap align-items-center justify-content-between search-items-body">
                                        <div class="cont d-flex flex-column gap-0">
                                            <p class="fs-14 text-dark mb-0 max-w-440 line--limit-1">B39 Bluetooth 5.0
                                                Headphone Ear Shape Wireless Headset phone Ear Shape </p>
                                            <div class="fs-12">Stock Qty : <span class="text-dark">10</span></div>
                                        </div>
                                        <div class="text-sm-right cont d-flex flex-column gap-0">
                                            <div class="text-dark fs-12 text-title text-nowrap">Unit Price</div>
                                            <div class="d-flex align-items-center gap-1">
                                                <h6 class="m-0 font-semibold text-dark fs-14">129.40$</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="d-flex flex-wrap gap-3 align-items-center mb-10px">
                    <h6 class="m-0">Products List </h6>
                    <span class="badge badge-soft-dark rounded-50 fz-10">3</span>
                </div>
                <div class="table-responsive pt-0 card mb-20">
                    <table
                        class="table table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer mb-0">
                        <thead class="border-0 table-th-bg p-0">
                        <tr>
                            <th class="border-0">Sl</th>
                            <th class="border-0">Item Description</th>
                            <th class="border-0 text-center">Qty</th>
                            <th class="border-0 text-right">Total</th>
                            <th class="border-0">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="custom__tr active">
                            <td>
                                <div>
                                    1
                                </div>
                            </td>
                            <td>
                                <div class="list-items-media cursor-pointer d-flex align-items-center gap-2 quick-View"
                                     data-id="57">
                                    <img width="50" height="50"
                                         src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="image"
                                         class="rounded">
                                    <div class="cont d-flex align-justify-content-center flex-column gap-0">
                                        <p class="fs-12 text-dark mb-1 max-w-220 line--limit-1">B39 Bluetooth 5.0
                                            Headphone Ear Shape Wireless Headset phone Ear Shape</p>
                                        <div class="d-flex align-items-center gap-1 fs-12">
                                            Unit Price : <span class="text-dark">129.40$</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 fs-12">
                                            Variation : <span class="text-dark">Black-42</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="product-quantity min-w-100 mx-auto">
                                    <div
                                        class="input-group bg-white rounded border d-flex justify-content-center align-items-center">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="minus">
                                                <i class="tio-remove  font-weight-bold"></i>
                                            </button>
                                        </span>
                                        <input type="text"
                                               class="w-25px input-number form-control p-0 border-0 text-center text-dark"
                                               placeholder="1" min="1" data-maximum_quantity="150">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="plus">
                                                <i class="tio-add  font-weight-bold"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fs-14 text-dark text-right">
                                125.00$
                            </td>
                            <td class="text-center">
                                <a class="btn btn-danger rounded-circle square-btn" href="javascript:"
                                   data-toggle="modal" data-target="#delete-product-modal">
                                    <i class="tio tio-delete"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="custom__tr">
                            <td>
                                <div>
                                    2
                                </div>
                            </td>
                            <td>
                                <div class="list-items-media cursor-pointer d-flex align-items-center gap-2 quick-View"
                                     data-id="57">
                                    <img width="50" height="50"
                                         src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="image"
                                         class="rounded">
                                    <div class="cont d-flex align-justify-content-center flex-column gap-0">
                                        <p class="fs-12 text-dark mb-1 max-w-220 line--limit-1">B39 Bluetooth 5.0
                                            Headphone Ear Shape Wireless Headset phone Ear Shape</p>
                                        <div class="d-flex align-items-center gap-1 fs-12">
                                            Unit Price : <span class="text-dark">129.40$</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="product-quantity min-w-100 mx-auto">
                                    <div
                                        class="input-group bg-white rounded border d-flex justify-content-center align-items-center">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="minus">
                                                <i class="tio-remove  font-weight-bold"></i>
                                            </button>
                                        </span>
                                        <input type="text"
                                               class="w-25px input-number form-control p-0 border-0 text-center text-dark"
                                               placeholder="1" min="1" data-maximum_quantity="150">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="plus">
                                                <i class="tio-add  font-weight-bold"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fs-14 text-dark text-right">
                                125.00$
                            </td>
                            <td class="text-center">
                                <a class="btn btn-danger rounded-circle square-btn" href="javascript:"
                                   data-toggle="modal" data-target="#delete-product-modal">
                                    <i class="tio tio-delete"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="custom__tr max-limit">
                            <td>
                                <div>
                                    3
                                </div>
                            </td>
                            <td>
                                <div class="list-items-media cursor-pointer d-flex align-items-center gap-2 quick-View"
                                     data-id="57">
                                    <img width="50" height="50"
                                         src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="image"
                                         class="rounded">
                                    <div class="cont d-flex align-justify-content-center flex-column gap-0">
                                        <p class="fs-12 text-dark mb-1 max-w-220 line--limit-1">B39 Bluetooth 5.0
                                            Headphone Ear Shape Wireless Headset phone Ear Shape</p>
                                        <div class="d-flex align-items-center gap-1 fs-12">
                                            Unit Price : <span class="text-dark">129.40$</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="product-quantity min-w-100 mx-auto">
                                    <div
                                        class="input-group bg-white rounded border d-flex justify-content-center align-items-center">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="minus">
                                                <i class="tio-remove  font-weight-bold"></i>
                                            </button>
                                        </span>
                                        <input type="text"
                                               class="w-25px input-number form-control p-0 border-0 text-center text-dark"
                                               placeholder="1" min="1" data-maximum_quantity="150">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="plus">
                                                <i class="tio-add  font-weight-bold"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fs-14 text-dark text-right">
                                125.00$
                            </td>
                            <td class="text-center">
                                <a class="btn btn-danger rounded-circle square-btn" href="javascript:"
                                   data-toggle="modal" data-target="#delete-product-modal">
                                    <i class="tio tio-delete"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="custom__tr">
                            <td>
                                <div>
                                    4
                                </div>
                            </td>
                            <td>
                                <div class="list-items-media cursor-pointer d-flex align-items-center gap-2 quick-View"
                                     data-id="57">
                                    <img width="50" height="50"
                                         src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="image"
                                         class="rounded">
                                    <div class="cont d-flex align-justify-content-center flex-column gap-0">
                                        <p class="fs-12 text-dark mb-1 max-w-220 line--limit-1">B39 Bluetooth 5.0
                                            Headphone Ear Shape Wireless Headset phone Ear Shape</p>
                                        <div class="d-flex align-items-center gap-1 fs-12">
                                            Unit Price : <span class="text-dark">129.40$</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="product-quantity min-w-100 mx-auto">
                                    <div
                                        class="input-group bg-white rounded border d-flex justify-content-center align-items-center">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="minus">
                                                <i class="tio-remove  font-weight-bold"></i>
                                            </button>
                                        </span>
                                        <input type="text"
                                               class="w-25px input-number form-control p-0 border-0 text-center text-dark"
                                               placeholder="1" min="1" data-maximum_quantity="150">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="plus">
                                                <i class="tio-add  font-weight-bold"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fs-14 text-dark text-right">
                                125.00$
                            </td>
                            <td class="text-center">
                                <a class="btn btn-danger rounded-circle square-btn" href="javascript:"
                                   data-toggle="modal" data-target="#delete-product-modal">
                                    <i class="tio tio-delete"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="custom__tr">
                            <td>
                                <div>
                                    5
                                </div>
                            </td>
                            <td>
                                <div class="list-items-media cursor-pointer d-flex align-items-center gap-2 quick-View"
                                     data-id="57">
                                    <img width="50" height="50"
                                         src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="image"
                                         class="rounded">
                                    <div class="cont d-flex align-justify-content-center flex-column gap-0">
                                        <p class="fs-12 text-dark mb-1 max-w-220 line--limit-1">B39 Bluetooth 5.0
                                            Headphone Ear Shape Wireless Headset phone Ear Shape</p>
                                        <div class="d-flex align-items-center gap-1 fs-12">
                                            Unit Price : <span class="text-dark">129.40$</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="product-quantity min-w-100 mx-auto">
                                    <div
                                        class="input-group bg-white rounded border d-flex justify-content-center align-items-center">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="minus">
                                                <i class="tio-remove  font-weight-bold"></i>
                                            </button>
                                        </span>
                                        <input type="text"
                                               class="w-25px input-number form-control p-0 border-0 text-center text-dark"
                                               placeholder="1" min="1" data-maximum_quantity="150">
                                        <span class="input-group-btn w-30px">
                                            <button class="btn px-2 btn-number bg-transparent w-30px" type="button"
                                                    data-type="plus">
                                                <i class="tio-add  font-weight-bold"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="fs-14 text-dark text-right">
                                125.00$
                            </td>
                            <td class="text-center">
                                <a class="btn btn-danger rounded-circle square-btn" href="javascript:"
                                   data-toggle="modal" data-target="#delete-product-modal">
                                    <i class="tio tio-delete"></i>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="offcanvas-footer w-100 bg-white p-3 d-flex align-items-center justify-content-end gap-3">
                <button type="button"
                        class="btn min-w-120 btn--secondary h--40px reset">{{ translate('Cancel') }}</button>
                <button type="submit" class="btn min-w-120 btn-primary h--40px">{{ translate('Update Cart ') }}</button>
            </div>
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>

@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $("form.fnd-validation").each(function () {
                const $form = $(this);
                const $inputs = $form.find('input, textarea, select').not('[type="file"]');
                const $fileInputs = $form.find('input[type="file"]');
                const $bio = $form.find('[name="bio"]');
                const $counter = $form.find('.counting-character');
                const $pass = $form.find('[name="password"]');

                // Initialize bio counter
                if ($bio.length && $counter.length) {
                    $counter.text('0/300');
                    $bio.on('input', function () {
                        const len = this.value.length;
                        $counter.text(`${len}/300`).toggleClass('limit-reached', len >= 300);
                    });
                }

                // Blur event for fields validation
                $inputs.on('blur', function () {
                    validateField($(this));
                });

                // File input validation on change
                $fileInputs.on('change', function () {
                    validateFile($(this));
                });

                // Reset button: reset form & hide errors & reset counter
                $form.find('button[type="reset"]').on('click', function () {
                    $form[0].reset();
                    $counter.text('0/300').removeClass('limit-reached');
                    $form.find('.typing-error').hide();
                });

                // Submit event
                $form.on('submit', function (e) {
                    let valid = true;

                    $inputs.each(function () {
                        const $el = $(this);
                        if ($el.closest('.without-validation').length === 0) {
                            if (!validateField($el)) {
                                valid = false;
                                console.log('Field invalid:', $el.attr('name'));
                            }
                        }
                    });

                    $fileInputs.each(function () {
                        const $el = $(this);
                        if ($el.closest('.without-validation').length === 0) {
                            if (!validateFile($el)) {
                                valid = false;
                                console.log('File invalid:', $el.attr('name'));
                            }
                        }
                    });

                    if (!valid) {
                        e.preventDefault(); // block submit ONLY if invalid
                    }
                    // if valid, do NOT preventDefault, so form submits and page reloads
                });

                // Validate normal fields
                function validateField($el) {
                    const val = $.trim($el.val());
                    const name = $el.attr('name');
                    const $error = $el.closest('.form-group, .upload--img-wrap').find('.typing-error');
                    let ok = true;

                    if (['name', 'brand_selection'].includes(name)) {
                        ok = val !== '';
                    } else if (name === 'email') {
                        ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                    } else if (name === 'bio') {
                        ok = val.length >= 5;
                    } else if (name === 'phone') {
                        ok = /^\+?[0-9\s\-()]{7,20}$/.test(val);
                    } else if (name === 'password') {
                        ok = val.length >= 8;
                    } else if (name === 'confirm_password') {
                        ok = val.length >= 8 && val === $pass.val();
                    } else if (name === 'country_select') {
                        if ($el.is('select')) {
                            ok = val !== '';
                        } else if ($el.attr('type') === 'tel') {
                            ok = /^\+?[0-9\s\-()]{7,20}$/.test(val);
                        } else {
                            ok = val !== '';
                        }
                    } else {
                        ok = val !== '';
                    }

                    if ($error.length) $error.css('display', ok ? 'none' : 'flex');
                    return ok;
                }

                // Validate file inputs
                function validateFile($input) {
                    const $error = $input.closest('.form-group, .upload--img-wrap').find('.typing-error');
                    const file = $input[0].files[0];
                    let ok = true;

                    if (!file) {
                        ok = true; // empty file input allowed
                    } else {
                        const types = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff'];
                        ok = types.includes(file.type) && file.size <= 5 * 1024 * 1024;
                    }

                    if ($error.length) $error.css('display', ok ? 'none' : 'flex');
                    return ok;
                }
            });
        });
    </script>
@endpush
