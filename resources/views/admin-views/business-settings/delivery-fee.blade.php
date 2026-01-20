@extends('layouts.admin.app')

@section('title', translate('delivery_fee'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business_setup') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>
        <div class="card mt-5">
            <div class="card-header">
                <h5 class="card-title">
                    <span>{{translate('Branch Wise Delivery Fee Setup')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="branchTabs" role="tablist">
                    @foreach($branches as $branch)
                        <li class="nav-item">
                            <a class="nav-link" id="branch{{ $branch->id }}-tab" data-toggle="tab" href="#branch{{ $branch->id }}" role="tab"
                               aria-controls="branch{{ $branch->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $branch->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="branchTabsContent">
                    @foreach($branches as $branch)
                        <div class="tab-pane fade" id="branch{{ $branch->id }}" role="tabpanel" aria-labelledby="branch{{ $branch->id }}-tab">
                            @include('admin-views.business-settings.partial.delivery_charge_form', ['branch' => $branch])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).ready(function() {
            $('#addAreaModal').on('shown.bs.modal', function () {
                $('#areaName').trigger('focus');
            });

            $('[data-toggle="modal"]').on('click', function() {
                var branchId = $(this).data('branch-id');
                $('.branchIdInput').val(branchId);
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var tabId = $(e.target).attr('href'); // e.g., #branch1
                localStorage.setItem('activeTab', tabId);

            });

            var activeTab = localStorage.getItem('activeTab');

            if (activeTab) {
                $('#branchTabs a[href="' + activeTab + '"]').tab('show');
            } else {
                $('#branchTabs a:first').tab('show');
            }
        });

    </script>

@endpush
