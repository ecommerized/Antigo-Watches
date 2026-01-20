@extends('layouts.admin.app')

@section('title', translate('Settings'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/system-setting.png')}}" alt="{{ translate('system_setting_image') }}">
                {{translate('system_setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.system-setup-nav')

        </div>

        <div class="alert alert-soft-danger mb-4" role="alert">
            {{translate('This_page_contains_sensitive_information.Make_sure_before_changing.')}}
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.clean-db')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        @foreach($tables as $table)
                            <div class="col-xl-3 col-lg-4 col-sm-6">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <input type="checkbox" name="tables[]" value="{{$table['name']}}" id="{{$table['name']}}">
                                    <label class="form-check-label text-dark" for="{{$table['name']}}">{{ Str::limit($table['name'], 20) }}</label>
                                    <span class="badge-pill badge-secondary fs-10">{{$table['rows']}}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                            class="btn btn-primary demo-form-submit">{{translate('Clear')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

    <script>
        $("form").on('submit',function(e) {
            e.preventDefault();
            Swal.fire({
                title: '{{translate('Are you sure?')}}',
                text: "{{translate('Sensitive_data! Make_sure_before_changing.')}}",
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#673ab7',
                cancelButtonText: '{{translate("No")}}',
                confirmButtonText: '{{translate("Yes")}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.submit();
                }else{
                    e.preventDefault();
                    toastr.success("{{translate('Cancelled')}}");
                    location.reload();
                }
            })
        });
    </script>
@endpush
