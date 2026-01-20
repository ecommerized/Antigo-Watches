<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-3">
            <div>
                <h5 class="mb-0">{{translate('Setup Fixed Delivery Charge')}}</h5>
                <p class="mb-0">{{ translate('Setup fixed delivery charge you want to deliver from store') }}</p>
            </div>

            <label class="toggle-switch h--45px toggle-switch-sm rounded">
                <input type="checkbox" class="toggle-switch-input change-delivery-charge-type-{{$branch->id}}" name="delivery_charge_type"
                       {{ $branch?->delivery_charge_setup?->delivery_charge_type == 'fixed' ? 'checked' : '' }}
                       data-type="fixed"
                       data-branch-id="{{$branch->id}}"
                       id="toggleFixed-{{$branch->id}}">
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </div>
    </div>
</div>

<div class="card mt-4" id="fixedDeliverySection-{{$branch->id}}">
    <div class="card-body">
        <form action="{{ route('admin.business-settings.store-fixed-delivery-charge') }}" method="POST">
            @csrf
            <div class="row">
                <input type="hidden" name="branch_id" id="" value="{{ $branch->id }}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fixed_delivery_charge">{{ translate('Fixed Delivery Charge') }} ({{ Helpers::currency_symbol() }})</label>
                        <input type="number" class="form-control" name="fixed_delivery_charge" min="0.1" max="99999999" step="0.1"
                               value="{{ $branch?->delivery_charge_setup?->fixed_delivery_charge }}" id="fixed_delivery_charge" placeholder="Ex: 10" required>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-3">
                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary demo-form-submit">{{translate('Save')}}</button>
            </div>
        </form>
    </div>
</div>


<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-3">
            <div>
                <h5 class="mb-0">{{translate('Setup Kilometer Wise Delivery Charge')}}</h5>
                <p class="mb-0">{{ translate('Setup delivery charges for per km  and how far you want to deliver from store') }}</p>
            </div>

            <label class="toggle-switch h--45px toggle-switch-sm rounded">
                <input type="checkbox" class="toggle-switch-input change-delivery-charge-type-{{$branch->id}}" name="delivery_charge_type"
                       {{ $branch?->delivery_charge_setup?->delivery_charge_type == 'distance' ? 'checked' : '' }}
                       data-type="distance"
                       data-branch-id="{{$branch->id}}"
                       id="toggleKilometerWise-{{$branch->id}}">
                <span class="toggle-switch-label text">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </div>
    </div>
</div>

<div class="card mt-4" id="kilometerWiseSection-{{$branch->id}}">
    <div class="card-body">
        <form action="{{ route('admin.business-settings.store-kilometer-wise-delivery-charge') }}" method="POST">
            @csrf
            <div class="row">
                <input type="hidden" name="branch_id" id="" value="{{ $branch->id }}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="per_km_charge">{{ translate('Per KM Delivery Charge') }} ({{ Helpers::currency_symbol() }})</label>
                        <input type="number" class="form-control" name="delivery_charge_per_kilometer" min="1" max="99999999" step="0.1"
                               value="{{ $branch?->delivery_charge_setup?->delivery_charge_per_kilometer }}" id="delivery_charge_per_kilometer" placeholder="Ex: 10" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="min_delivery_charge">{{ translate('Minimum Delivery Charge') }} ({{ Helpers::currency_symbol() }})</label>
                        <input type="number" class="form-control" name="minimum_delivery_charge" min="0" max="99999999" step="0.1"
                               value="{{ $branch?->delivery_charge_setup?->minimum_delivery_charge }}" id="minimum_delivery_charge" placeholder="Ex: 10" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="min_distance_free_delivery">{{ translate('Distance Area for Free Delivery') }} (Km)</label>
                        <input type="number" class="form-control" name="minimum_distance_for_free_delivery" min="0" max="99999999" step="0.01"
                               value="{{ $branch?->delivery_charge_setup?->minimum_distance_for_free_delivery }}" id="minimum_distance_for_free_delivery" placeholder="Ex: 10" required>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-3">
                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary demo-form-submit">{{translate('Save')}}</button>
            </div>
        </form>
    </div>
</div>

<?php
$areaCount = $branch->delivery_charge_by_area->count()
?>
<div class="card my-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-3">
            <div>
                <h5 class="mb-0">{{translate('Setup Area/Zip Code Wise Delivery Charge')}}</h5>
                <p class="mb-0">{{ translate('Create Area/Zip Code wise delivery region and specify the charges for each region') }}</p>
            </div>

            <label class="toggle-switch h--45px toggle-switch-sm rounded">
                <input type="checkbox" class="toggle-switch-input  @if($areaCount > 0) change-delivery-charge-type-{{$branch->id}} @else change-delivery-charge-to-area-{{$branch->id}} @endif"
                       name="delivery_charge_type"
                       {{ $branch?->delivery_charge_setup?->delivery_charge_type == 'area' ? 'checked' : '' }}
                       data-type="area"
                       data-branch-id="{{$branch->id}}"
                       id="toggleAreaWise-{{$branch->id}}">
                <span class="toggle-switch-label text">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </div>

        <div id="areaWiseSection-{{$branch->id}}">
            <div class="d-flex flex-wrap gap-2 align-items-center my-4">
                <h4 class="mb-0 d-flex align-items-center gap-2">
                    {{translate('Area/Zip Code List')}}
                </h4>
                <span class="badge badge-soft-dark rounded-circle fz-12">{{ $branch->delivery_charge_by_area->count() }}</span>
            </div>

            <div class="row gx-2 gx-lg-3">
                <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                    <div class="card px-3">
                        <div class="card-top px-card pt-4">
                            <div class="d-flex flex-column flex-md-row flex-wrap gap-3 justify-content-md-between align-items-md-center">
                                <form action="{{url()->current()}}" method="GET">
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="search" class="form-control min-width-260px"
                                               placeholder="{{translate('Search by area name or zip code')}}"
                                               aria-label="Search" value="{{ request()->input('search') }}"  autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn--primary">
                                                {{translate('Search')}}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="d-flex flex-wrap justify-content-md-end gap-3">
                                    <div>
                                        <a type="button" class="btn btn-outline-primary text-nowrap"
                                           href="{{ route('admin.business-settings.export-area-delivery-charge',[$branch->id]).  (request('search') ? '?search=' . request('search') : '') }}">
                                            <i class="tio-upload"></i>
                                            {{ translate('Export') }}
                                        </a>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary text-nowrap" data-toggle="modal" data-target="#importConfirmModal-{{$branch->id}}">
                                            <i class="tio-download-to"></i>
                                            {{ translate('Import') }}
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn--primary" data-toggle="modal" data-target="#addAreaModal-{{$branch->id}}" data-id="{{$branch->id}}">
                                        <i class="tio-add"></i>
                                        {{translate('Add Area/Zip Code')}}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="py-4">
                            <div class="table-responsive datatable-custom">
                                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{translate('SL')}}</th>
                                        <th>{{translate('Area Name/Zip Code')}}</th>
                                        <th>{{translate('Delivery Charge')}} ({{ Helpers::currency_symbol() }})</th>
                                        <th class="text-center">{{translate('action')}}</th>
                                    </tr>
                                    </thead>

                                    <tbody id="set-rows">
                                        @forelse($branch->delivery_charge_by_area as $key => $deliveryArea)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $deliveryArea->area_name }}</td>
                                                <td>{{ Helpers::set_symbol($deliveryArea->delivery_charge) }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-3">
                                                        <a class="btn btn-outline-info btn-sm edit square-btn edit-area"
                                                           data-toggle="modal" data-target="#editDeliveryChargeModal-{{$branch->id}}"
                                                           data-id="{{$deliveryArea->id}}"
                                                           href="#">
                                                            <i class="tio-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger btn-sm delete square-btn form-alert"
                                                                data-id="area-{{$deliveryArea->id}}" data-message="{{translate('Want to remove this Area')}}?">
                                                            <i class="tio-delete"></i>
                                                        </button>
                                                        <form action="{{route('admin.business-settings.delete-area-delivery-charge',[$deliveryArea->id, 'branch_id' => $deliveryArea->branch_id])}}"
                                                              method="post" id="area-{{$deliveryArea->id}}">
                                                            @csrf @method('delete')
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <img class="my-4" src="{{ asset('public/assets/admin/svg/components/map.svg') }}" alt="{{ translate('info icon') }}">
                                                    <h4>{{ translate('Create Area/Zip Code') }}</h4>
                                                    <p>{{ translate('Create area/zip code and setup delivery charge') }}</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="confirmChangeModalToArea-{{$branch->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmChangeModalToArea" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('public/assets/admin/svg/components/info.svg') }}" alt="{{ translate('image') }}" class="mb-4">
                    <h4>{{ translate('Are You Sure') }}?</h4>
                    <p>{{ translate('Do you want to change the delivery charge setup? You can only use one setup at a time. When you switch to a new setup, the previous one is automatically deactivated.') }}</p>

                    <p>{{ translate('You must create at least one Zipcode & Its Delivery Charge to enable this delivery charge option.') }}</p>

                </div>

                <div class="text-center mb-4">
                    <h5 id="addAreaModalLabel">{{ translate('Add New Area/Zip Code & Delivery Charge') }}</h5>
                </div>
                <div class="my-2">
                    <form action="{{ route('admin.business-settings.store-delivery-wise-delivery-charge', ['change_status' => 1]) }}" method="POST">
                        @csrf
                        <div class="row bg-soft-secondary py-3">
                            <input type="hidden" name="branch_id" class="branchIdInput" id="branchIdInput-{{$branch->id}}" value="{{$branch->id}}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="areaName">{{ translate('Zip Code / Area Name') }}</label>
                                    <input type="text" class="form-control" id="areaName" name="area_name" min="0" max="99999999" step="0.1" placeholder="Ex: 1216" maxlength="255" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deliveryCharge">{{ translate('Delivery Charge') }} ({{ Helpers::currency_symbol() }})</label>
                                    <input type="number" class="form-control" id="deliveryCharge" name="delivery_charge" min="0.01" step="0.01" placeholder="Ex: $20" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mt-4 justify-content-center gap-3">
                            <button type="reset" class="btn btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="editDeliveryChargeModal-{{$branch->id}}" tabindex="-1" aria-labelledby="editDeliveryChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h5 id="editDeliveryChargeModalLabel">{{ translate('Edit Area Name/Zip Code & Delivery Charge') }}</h5>
                    </div>
                    <div class="my-2">
                        <form id="editDeliveryChargeForm-{{$branch->id}}" method="POST">
                            @csrf
                            <div class="row bg-soft-secondary py-3">
                                <div class="col-md-6">
                                    <label for="areaName" class="form-label">{{ translate('Zip Code / Area Name') }}</label>
                                    <input type="text" class="form-control" id="areaName-{{$branch->id}}" name="area_name" placeholder="Enter area name or zip code">
                                </div>
                                <div class="col-md-6">
                                    <label for="deliveryCharge" class="form-label">{{ translate('Delivery Charge') }}({{ Helpers::currency_symbol() }})</label>
                                    <input type="number" class="form-control" id="deliveryCharge-{{$branch->id}}" name="delivery_charge" min="0.01" step="0.01" placeholder="Enter delivery charge">
                                </div>
                            </div>
                            <div class="d-flex mt-4 justify-content-center gap-3">
                                <button type="reset" class="btn btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary demo-form-submit" id="saveChangesButton">{{translate('Update')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addAreaModal-{{$branch->id}}" tabindex="-1" role="dialog" aria-labelledby="addAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h5 id="addAreaModalLabel">{{ translate('Add New Area/Zip Code & Delivery Charge') }}</h5>
                </div>
                <div class="my-2">
                    <form action="{{ route('admin.business-settings.store-delivery-wise-delivery-charge') }}" method="POST">
                        @csrf
                        <div class="row bg-soft-secondary py-3">
                            <input type="hidden" name="branch_id" class="branchIdInput" id="branchIdInput-{{$branch->id}}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="areaName">{{ translate('Zip Code / Area Name') }}</label>
                                    <input type="text" class="form-control" id="areaName" name="area_name" placeholder="Ex: 1216" maxlength="255" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deliveryCharge">{{ translate('Delivery Charge') }} ({{ Helpers::currency_symbol() }})</label>
                                    <input type="number" class="form-control" id="deliveryCharge" name="delivery_charge" placeholder="Ex: $20" min="0" max="99999999" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mt-4 justify-content-center gap-3">
                            <button type="reset" class="btn btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary demo-form-submit">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- import Modal -->
<div class="modal fade" id="importConfirmModal-{{$branch->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="importForm-{{$branch->id}}">
                @csrf
                <div class="modal-body">
                    <div class="text-center my-4">
                        <img src="{{ asset('public/assets/admin/svg/components/file.svg') }}" alt="{{ translate('image') }}" class="mb-4">
                        <h4>{{ translate('Add New or Replace in the List') }}</h4>
                        <p>{{ translate('You can download the example file to understand how the file must be filled with proper data.') }}
                            <a href="{{asset('public/assets/area_bulk_format.xlsx')}}" download="" class="fz-16 btn-link">
                                {{translate('Download Format')}}
                            </a>
                        </p>
                        <p>{{ translate('To upload and add new data to your list, click the "Add New" button. To replace the existing list, click the "Replace" button.') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="area_list">{{ translate('Import Area/Zip Code') }}</label>
                        <input type="file" class="form-control" id="area_list-{{$branch->id}}" name="area_file" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="d-flex mb-4 justify-content-center gap-3">
                    <button type="button" data-type="replace" class="btn btn--reset import-button-{{$branch->id}}" id="replace-{{$branch->id}}">{{ translate('Replace') }}</button>
                    <button type="button" data-type="new" class="btn btn--primary import-button-{{$branch->id}}" id="addNew-{{$branch->id}}">{{ translate('Add New') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Turning On a New Setup -->
<div class="modal fade" id="confirmChangeModal-{{$branch->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('public/assets/admin/svg/components/info.svg') }}" alt="{{ translate('image') }}" class="mb-4">
                <h4>{{ translate('Are You Sure') }}?</h4>
                <p>{{ translate('Do you want to change the delivery charge setup? You can only use one setup at a time. When you switch to a new setup, the previous one is automatically deactivated.') }}</p>
            </div>
            <div class="d-flex flex-wrap mb-4 justify-content-center gap-3">
                <button type="button" class="btn btn--reset" id="cancelChange-{{$branch->id}}">{{ translate('Cancel') }}</button>
                <button type="button" class="btn btn--primary" id="confirmChange-{{$branch->id}}">{{ translate('Yes') }}, {{ translate('Change') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Choosing New Setup When Turning Off -->
<div class="modal fade" id="deactivationModal-{{$branch->id}}" tabindex="-1" role="dialog" aria-labelledby="deactivationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('public/assets/admin/svg/components/info.svg') }}" alt="{{ translate('image') }}" class="mb-4">
                    <h4>{{ translate('To Turn Off, Select an Option Below.') }}</h4>
                    <p>{{ translate('If you want to turn off all setup, you need to choose one of the options below and continue. Without this, the delivery charge can’t work.') }}</p>
                </div>
                <!-- Delivery charge options -->
                <div class="bg-soft-secondary mx-6 p-3">
                    <div id="option-fixed-{{$branch->id}}" class="delivery-option mt-2">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="option-fixed-radio-{{$branch->id}}" name="new_delivery_charge_type" value="fixed" class="custom-control-input">
                            <label class="custom-control-label" for="option-fixed-radio-{{$branch->id}}">{{ translate('Fixed Delivery Charge Setup') }}</label>
                        </div>
                    </div>

                    <div id="option-distance-{{$branch->id}}" class="delivery-option mt-2">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="option-distance-radio-{{$branch->id}}" name="new_delivery_charge_type" value="distance" class="custom-control-input">
                            <label class="custom-control-label" for="option-distance-radio-{{$branch->id}}">{{ translate('Kilometer Wise Delivery Charge Setup') }}</label>
                        </div>
                    </div>

                    <div id="option-area-{{$branch->id}}" class="delivery-option mt-2">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="option-area-radio-{{$branch->id}}" name="new_delivery_charge_type" value="area" class="custom-control-input">
                            <label class="custom-control-label" for="option-area-radio-{{$branch->id}}">{{ translate('Area/Zip Code Wise Delivery Charge Setup') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex mb-4 justify-content-center gap-3">
                <button type="button" class="btn btn--reset" id="cancelDeactivation-{{$branch->id}}">{{ translate('Cancel') }}</button>
                <button type="button" class="btn btn--primary" id="confirmDeactivation-{{$branch->id}}">{{ translate('Continue') }}</button>
            </div>
        </div>
    </div>
</div>


@push('script_2')
    <script>
        $(document).ready(function() {
            function toggleSection(){
                $('#kilometerWiseSection-{{$branch->id}}').toggle($('#toggleKilometerWise-{{$branch->id}}').is(':checked'));
                $('#toggleKilometerWise-{{$branch->id}}').change(function() {
                    $('#kilometerWiseSection-{{$branch->id}}').toggle(this.checked);
                });

                $('#areaWiseSection-{{$branch->id}}').toggle($('#toggleAreaWise-{{$branch->id}}').is(':checked'));
                $('#toggleAreaWise-{{$branch->id}}').change(function() {
                    $('#areaWiseSection-{{$branch->id}}').toggle(this.checked);
                });

                $('#fixedDeliverySection-{{$branch->id}}').toggle($('#toggleFixed-{{$branch->id}}').is(':checked'));
                $('#toggleFixed-{{$branch->id}}').change(function() {
                    $('#fixedDeliverySection-{{$branch->id}}').toggle(this.checked);
                });
            }
            toggleSection();


            $('[data-toggle="modal"]').on('click', function() {
                var branchId = $(this).data('id');
                $('#addAreaModal-' + branchId).on('shown.bs.modal', function () {
                    $(this).find('.branchIdInput').val(branchId);
                });
            });

            $('.edit-area').on('click', function() {
                var deliveryAreaId = $(this).data('id');
                var branchId = $(this).data('target').split('-').pop();

                var actionUrl = '/admin/business-settings/update-area-delivery-charge/' + deliveryAreaId;
                $('#editDeliveryChargeForm-' + branchId).attr('action', actionUrl);

                $.ajax({
                    url: '/admin/business-settings/edit-area-delivery-charge/' + deliveryAreaId,
                    method: 'GET',
                    success: function(data) {
                        $('#areaName-' + branchId).val(data.area_name);
                        $('#deliveryCharge-' + branchId).val(data.delivery_charge);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            let checkbox = null;
            let previousState = null;

            function showDeactivationModal() {
                const currentType = checkbox.data('type');

                $('.delivery-option').hide();
                $('input[name="new_delivery_charge_type"]').prop('checked', false);

                if (currentType === 'fixed') {
                    $('#option-distance-{{$branch->id}}, #option-area-{{$branch->id}}').show();
                    $('#option-distance-radio-{{$branch->id}}').prop('checked', true);
                } else if (currentType === 'distance') {
                    $('#option-fixed-{{$branch->id}}, #option-area-{{$branch->id}}').show();
                    $('#option-fixed-radio-{{$branch->id}}').prop('checked', true);
                } else if (currentType === 'area') {
                    $('#option-fixed-{{$branch->id}}, #option-distance-{{$branch->id}}').show();
                    $('#option-fixed-radio-{{$branch->id}}').prop('checked', true);
                }

                $('#deactivationModal-{{$branch->id}}').modal('show');
            }

            function confirmDeactivation() {
                if (checkbox) {
                    let deliveryChargeType = checkbox.data('type');
                    let branchId = checkbox.data('branch-id');
                    let status = 0;

                    // Get the selected new delivery charge type
                    let newDeliveryChargeType = $('input[name="new_delivery_charge_type"]:checked').val();

                    $.ajax({
                        url: "{{ route('admin.business-settings.change-delivery-charge-type') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            delivery_charge_type: deliveryChargeType,
                            branch_id: branchId,
                            status: status,
                            new_delivery_charge_type: newDeliveryChargeType
                        },
                        success: function(response) {
                            if(response.status != false){
                                toastr.success(response.message);
                                location.reload();
                            }else {
                                toastr.error(response.error);
                                checkbox.prop('checked', previousState);
                            }
                        },
                        error: function() {
                            checkbox.prop('checked', previousState);
                        }
                    });
                }
            }

            $('.change-delivery-charge-type-{{$branch->id}}').change(function() {
                checkbox = $(this);
                previousState = checkbox.is(':checked');

                if (!previousState) {
                    showDeactivationModal();
                } else {
                    // Show the existing modal for activation
                    $('#confirmChangeModal-{{$branch->id}}').modal('show');
                }
            });

            $('#confirmChange-{{$branch->id}}').click(function() {
                if (checkbox) {
                    let deliveryChargeType = checkbox.data('type');
                    let branchId = checkbox.data('branch-id');
                    let status = 1; // We're activating a new type

                    $.ajax({
                        url: "{{ route('admin.business-settings.change-delivery-charge-type') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            delivery_charge_type: deliveryChargeType,
                            branch_id: branchId,
                            status: status
                        },
                        success: function(response) {
                            console.log(response)
                            if(response.status != false){
                                toastr.success(response.message);
                                location.reload();
                            }else {
                                toastr.error(response.error);
                                checkbox.prop('checked', previousState);
                            }
                        },
                        error: function() {
                            checkbox.prop('checked', previousState);
                        }
                    });
                }
            });

            $('#confirmDeactivation-{{$branch->id}}').click(function() {
                confirmDeactivation();
            });


            $('#cancelChange-{{$branch->id}}').click(function() {
                if (checkbox) {
                    checkbox.prop('checked', previousState);
                }
                $('#confirmChangeModal-{{$branch->id}}').modal('hide');
            });

            $('#cancelDeactivation-{{$branch->id}}').click(function() {
                if (checkbox) {
                    checkbox.prop('checked', previousState);
                }
                $('#deactivationModal-{{$branch->id}}').modal('hide');
            });

            $('#deactivationModal-{{$branch->id}}, #confirmChangeModal-{{$branch->id}}').on('hidden.modal', function () {
                if (checkbox) {
                    checkbox.prop('checked', previousState);
                }
            });

            $('.change-delivery-charge-to-area-{{$branch->id}}').change(function() {
                checkbox = $(this);
                previousState = checkbox.is(':checked');

                if (!previousState) {
                } else {
                    $('#confirmChangeModalToArea-{{$branch->id}}').modal('show');
                }

                $('#confirmChangeModalToArea-{{$branch->id}}').on('hidden.bs.modal', function () {
                    if (checkbox) {
                        checkbox.prop('checked', !previousState);
                        toggleSection();
                    }
                });
            });


            $('.import-button-{{$branch->id}}').on('click', function() {
                var branchId = {{$branch->id}};
                var type = $(this).data('type');
                var form = $('#importForm-' + branchId)[0];
                var formData = new FormData(form);
                formData.append('type', type);

                $.ajax({
                    url: '/admin/business-settings/import-area-delivery-charge/' + branchId,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        let response = JSON.parse(xhr.responseText);
                        toastr.error(response.message);
                    }
                });
            });
        });

    </script>

@endpush
