@extends('layouts.admin.app')

@section('title', translate('Privacy policy'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/pages.png')}}" alt="{{ translate('pages') }}">
                {{translate('pages')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.page-nav')
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.privacy-policy')}}" method="post" id="privacy-policy-form">
                    @csrf

                    <div class="form-group">
                        <div id="editor" class="min-h-15">{!! $data['value'] !!}</div>
                        <textarea name="privacy_policy" id="hiddenArea" style="display:none;"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/quill-editor.js') }}"></script>
    <script>
        $(document).ready(function () {
            var bn_quill = new Quill('#editor', {
                theme: 'snow'
            });

            $('#privacy-policy-form').on('submit', function () {
                var myEditor = document.querySelector('#editor');
                $('#hiddenArea').val(myEditor.children[0].innerHTML);
            });
        });
    </script>
@endpush
