@extends('layouts.admin.app')

@section('title', translate('Update Flash sale'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="16" src="{{asset('public/assets/admin/img/icons/flash-sale.png')}}" alt="{{ translate('flash-sale') }}">
                {{translate('Update Flash sale')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.flash-sale.update', [$flashSale['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">{{translate('Title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ translate('Ex : LUX') }}"
                                       value="{{ $flashSale['title'] }}" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">{{ translate('Start Date')}}</label>
                                <input type="datetime-local" name="start_date" id="start_date" class="form-control"
                                       value="{{ date('Y-m-d\TH:i', strtotime($flashSale['start_date']))}}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">{{translate('End Date')}}</label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control"
                                       value="{{ date('Y-m-d\TH:i', strtotime($flashSale['end_date']))}}" required>
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

    <script>
        "use strict"

        let oldStartDate = '{{ date('Y-m-d\TH:i', strtotime($flashSale['start_date'])) }}';
        let oldEndDate = '{{ date('Y-m-d\TH:i', strtotime($flashSale['end_date'])) }}';

        $('#start_date, #end_date').change(function () {
            let from = $('#start_date').val();
            let to = $('#end_date').val();
            if (from != '' && to != '') {
                if (from > to) {
                    $('#start_date').val(oldStartDate);
                    $('#end_date').val(oldEndDate);
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });

    </script>

@endpush
