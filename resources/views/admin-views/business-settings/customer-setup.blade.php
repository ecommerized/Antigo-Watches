@extends('layouts.admin.app')

@section('title', translate('Customer Setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/business-setup.png')}}"
                     alt="{{ translate('business_setup_image') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="card">
            <div class="card-body">
                <div class="view-details-container">
                    <div class="d-flex gap-3 justify-content-end align-items-center flex-wrap">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{translate('customer_wallet')}}</h4>
                            <p class="fs-12 mb-0">{{translate('when_active_this_feature_customer_can_earn_&_buy_through_wallet._see_customer_wallet_from_customers_details_page.')}}</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <button type="button"
                                    class="btn font-weight-bold fs-12 text--primary p-0 view-btn"> {{ translate('View') }}
                                <i class=" {{$customerSetupWalletEarning && ($customerSetupWalletEarning['status'] ?? 0)==1?'tio-up-ui':'tio-down-ui'}} fs-10"></i></button>
                            <label class="switcher">
                                <input type="checkbox"
                                       class="switcher_input update-business-setting"
                                       id="customerSetupWalletEarning"
                                       name="status"
                                       data-url="{{route('admin.business-settings.customer-setup-update')}}"
                                       data-title="{{$customerSetupWalletEarning && ($customerSetupWalletEarning['status'] ?? 0)==1 ?
                                           translate('turn_off_customer_wallet?')  :  translate('turn_on_customer_wallet?') }}"
                                       data-sub-title="{{
                                         $customerSetupWalletEarning && ($customerSetupWalletEarning['status'] ?? 0)==1 ?
                                         translate('by_turn_off_the_customer_wallet_feature,_customer_will_not_get_any_wallet_page.'):
                                         translate('by_turn_on_the_customer_wallet_feature,_customer_will_get_a_wallet_page_where_they_can_see_their_reward_amounts.')
                                         }}"
                                       data-confirm-btn="{{$customerSetupWalletEarning && ($customerSetupWalletEarning['status'] ?? 0)==1 ? translate('Turn Off') : translate('Turn On')}}"
                                        {{$customerSetupWalletEarning && ($customerSetupWalletEarning['status'] ?? 0)==1?'checked':''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                    </div>
                    <div class="view-details mt-3" style="{{ $customerSetupWalletEarning && $customerSetupWalletEarning['status'] == 1 ? '' : 'display: none' }}">
                        <form id="login-setup-form" action="{{route('admin.business-settings.customer-setup-update')}}"
                              method="post">
                            @csrf
                            <div class="d-flex flex-column gap-20">
                                <div class="bg-light p-3 rounded-lg">
                                    <label> {{ translate('wallet_amount_earn_per_order') }} (%)
                                        <i class="tio-info-outlined" data-toggle="tooltip" data-placement="top" title=""
                                           data-original-title="{{ translate('wallet_amount__earn_per_order') }}">
                                        </i>
                                    </label>
                                    <input type="number" min="0" max="100" step="0.1"
                                           value="{{$customerSetupWalletEarning?$customerSetupWalletEarning['order_wise_earning_percentage']:""}}"
                                           name="order_wise_earning_percentage" class="form-control"
                                           placeholder="{{ translate('enter_percentage_of_the_order_value') }}"
                                           required=""></div>
                                <div class="badge badge-soft-info p-2 d-flex align-items-center gap-1 text-wrap text-left lh-1.3">
                                    <i class="tio-info"></i>
                                    <span class="text-dark opacity-lg font-size-sm">
                                         {{ translate('you_can_see_customer_wallet_from_customers_details_page.') }}
                                        {{ translate('go_to_this_path_') }}
                                         <span class="font-weight-bold">{{ translate("User_Management") .' > '. translate("Customer_List"). ' > '. translate("View_Details") }}</span>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-end flex-wrap gap-3">
                                    <button type="reset"
                                            class="btn btn-secondary max-w-120 flex-grow-1">{{ translate('Reset') }}</button>
                                    <button type="submit"
                                            class="btn btn-primary demo-form-submit max-w-120 flex-grow-1">{{ translate('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal --}}
    <div class="modal fade" id="toggleModal" tabindex="-1" role="dialog" aria-labelledby="toggleModalLabel"
         aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div>
                        <img width="70" height="70" class="aspect-1 "
                             src="{{ asset('public/assets/admin/img/icons/switch.svg') }}"
                             alt="{{ translate('image') }}">
                    </div>
                    <div class="py-4">
                        <h3 class="modal-title" id="title"></h3>
                    </div>
                    <p id="subTitle"></p>
                </div>
                <div class="text d-flex flex-wrap align-items-center justify-content-center gap-3 text-center mb-5">
                    <button type="button" id="modalCancelBtn" data-dismiss="modal"
                            class="btn btn--secondary max-w-120 flex-grow-1">{{ translate('No') }}</button>
                    <button type="button" id="modalConfirmBtn"
                            class="btn btn-primary max-w-120 flex-grow-1 yesBtn">{{ translate('Yes') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')

    <script>
        $(document).ready(function () {
            let currentSwitcher = null;
            let originalChecked = false;

            // --- view button functionality ---
            $(".view-btn").on("click", function () {
                let container = $(this).closest(".view-details-container");
                let details = container.find(".view-details");
                let icon = $(this).find("i");

                $(this).toggleClass("active");
                if ($(this).hasClass("active")) {
                    details.slideDown(300);
                    icon.removeClass("tio-down-ui").addClass("tio-up-ui");
                } else {
                    details.slideUp(300);
                    icon.removeClass("tio-up-ui").addClass("tio-down-ui");
                }
            });

        });

        $('.update-business-setting').on('click', function (e) {
            updateBusinessSetting(this, e)
        });


        function updateBusinessSetting(obj, e) {
            e.preventDefault();
            currentSwitcher = $(obj);
            originalChecked = $(obj).prop("checked");
            let url = $(obj).data('url');
            let titleContent = $(obj).data('title');
            let subTitleContent = $(obj).data('sub-title');
            let value = $(obj).prop('checked') === true ? 1 : 0;
            // Show custom modal
            const modalElement = document.getElementById('toggleModal');
            let bootstrapModal = new bootstrap.Modal(modalElement);
            bootstrapModal.show();
            if (titleContent) {
                $("#title").html("");
                $("#title").html(titleContent);
            }
            if (subTitleContent) {
                $("#subTitle").html("");
                $("#subTitle").html(subTitleContent);
            }

            let confirmBtn = document.getElementById("modalConfirmBtn");

            confirmBtn.onclick = function () {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {_token: '{{ csrf_token() }}',status: value},
                    success: function () {
                        toastr.success("{{ translate('status_changed_successfully') }}");
                        if (currentSwitcher) {
                            let container = currentSwitcher.closest(".view-details-container");
                            let details = container.find(".view-details");
                            let viewBtn = container.find(".view-btn");
                            let icon = viewBtn.find("i");

                            let isChecked = currentSwitcher.prop("checked");
                            currentSwitcher.prop("checked", !isChecked);

                            // open/close details based on new status
                            if (!isChecked) {
                                details.slideDown(300);
                                viewBtn.addClass("active");
                                icon.removeClass("tio-down-ui").addClass("tio-up-ui");
                            } else {
                                details.slideUp(300);
                                viewBtn.removeClass("active");
                                icon.removeClass("tio-up-ui").addClass("tio-down-ui");
                            }
                        }
                        $("#toggleModal").modal("hide");
                        setTimeout(function () {
                            location.reload();
                        }, 500);

                    },
                    error: function (response) {
                        console.log(response.errors);
                        toastr.error("{{ translate('status_change_failed') }}");
                    }
                });
                bootstrapModal.hide();
            }
        }

    </script>
@endpush
