@extends('layouts.admin.app')

@section('title', translate('Product Bulk Import'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/bulk-import.png')}}" alt="{{ translate('bulk-import') }}">
                {{translate('bulk_import')}}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h2 class="display-4 mb-3">{{ translate('Instructions') }} : </h2>
                <ol class="d-flex flex-column gap-2 pl-4">
                    <li>{{ translate(' Download the format file and fill it with proper data.') }}</li>
                    <li>{{ translate(' You can download the example file to understand how the data must be filled.') }}</li>
                    <li>{{ translate(' Once you have downloaded and filled the format file, upload it in the form below and submit.') }}</li>
                    <li>{{ translate(" After uploading products you need to edit them and set product's images and choices.") }}</li>
                    <li>{{ translate(' You can get category and sub-category id from their list, please input the right ids.') }}</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form class="product-form" action="{{route('admin.product.bulk-import')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="rest-part">
                        <div class="d-flex flex-wrap justify-content-center gap-3 align-items-center mb-4">
                            <h4 class="mb-0">{{ translate('Don`t have the template') }}?</h4>
                            <a href="{{asset('public/assets/product_bulk_format.xlsx')}}" download=""
                            class="text-primary font-weight-bold fs-16">{{ translate('Download Format') }}</a>
                        </div>
                        <div class="form-group">
                            <div class="row justify-content-center">
                                <div class="col-auto">
                                    <div class="upload-file">
                                        <input type="file" name="products_file" accept=".xlsx"
                                               class="upload-file__input" data-maxFileSize="{{ readableUploadMaxFileSize('file') }}">
                                        <div class="upload-file__img_drag upload-file__img">
                                            <img src="{{asset('public/assets/admin/img/icons/drag-upload-file.png')}}" alt="{{ translate('upload') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
