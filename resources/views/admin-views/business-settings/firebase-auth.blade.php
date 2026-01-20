@extends('layouts.admin.app')

@section('title', translate('Firebase Auth'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/third-party.png')}}" alt="{{ translate('3rd_Party_image') }}">
                {{translate('3rd_Party')}}
            </h2>
        </div>

        <div class="inline-page-menu my-4">
            @include('admin-views.business-settings.partial.third-party-nav')
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-firebase-auth')}}" method="post">
                    @csrf
                    <div class="row">
                        <?php
                            $firebaseOtp=\App\CentralLogics\Helpers::get_business_settings('firebase_otp_verification');
                        ?>
                        <div class="col-md-6 mt-5">
                            <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                <h5 class="mb-0">{{translate('Firebase Auth Verification Status')}}</h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" name="status" id="firebase_auth_status" {{ isset($firebaseOtp) && $firebaseOtp['status'] == 1 ? 'checked' : '' }}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="otp_resend_time">{{translate('web_api_key')}}</label>
                                <input type="text" value="{{$firebaseOtp && env('APP_MODE')!='demo' ? $firebaseOtp['web_api_key'] : ''}}" name="web_api_key" id="web_api_key"
                                       class="form-control" placeholder="">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                class="btn btn-primary demo-form-submit">{{translate('update')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>


@endsection

@push('script_2')
<script>
    $(document).ready(function () {
        const $firebaseAuthStatus = $('#firebase_auth_status');
        const $webApiKeyInput = $('#web_api_key');

        // Function to toggle the readonly state of the input field
        function toggleWebApiKey() {
            const isChecked = $firebaseAuthStatus.is(':checked');
            if (isChecked) {
                $webApiKeyInput.prop('readonly', false);  // Make editable
            } else {
                $webApiKeyInput.prop('readonly', true);   // Make readonly but keep value
            }
        }

        // Initial call to set the correct state on page load
        toggleWebApiKey();

        // Add event listener to handle checkbox changes
        $firebaseAuthStatus.on('change', toggleWebApiKey);
    });
</script>
@endpush
