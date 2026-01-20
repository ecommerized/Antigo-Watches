@extends('layouts.admin.app')

@section('title', translate('Social Media Settings'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/social_media.png')}}" alt="{{ translate('social_media') }}">
                {{translate('Social_Media')}}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">{{ translate('social_media_form')}}</h5>
            </div>
            <div class="card-body">
                <form>
                    @csrf
                    <div class="form-group">
                        <label for="name">{{translate('name')}}</label>
                        <select class="form-control" name="name" id="name">
                            <option>---{{translate('select')}}---</option>
                            <option value="instagram">{{translate('Instagram')}}</option>
                            <option value="facebook">{{translate('Facebook')}}</option>
                            <option value="twitter">{{translate('Twitter')}}</option>
                            <option value="linkedin">{{translate('LinkedIn')}}</option>
                            <option value="pinterest">{{translate('Pinterest')}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="id">
                        <label for="link">{{ translate('social_media_link')}}</label>
                        <input type="text" name="link" class="form-control" id="link"
                                placeholder="{{translate('Enter Social Media Link')}}" required maxlength="255">
                    </div>
                    <div>
                        <input type="hidden" id="id">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button id="add" class="btn btn-primary">{{ translate('save')}}</button>
                        <a id="update" class="btn btn-primary d--none">{{ translate('update')}}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ translate('social_media_table')}}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">{{ translate('sl')}}</th>
                                <th scope="col">{{ translate('name')}}</th>
                                <th scope="col">{{ translate('link')}}</th>
                                <th scope="col">{{ translate('status')}}</th>
                                <th class="w-100px" scope="col">{{ translate('action')}}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict"

        fetch_social_media();

        function fetch_social_media() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.fetch')}}",
                method: 'GET',
                success: function (data) {

                    if (data.length != 0) {
                        let html = '';
                        for (let count = 0; count < data.length; count++) {
                            html += '<tr>';
                            html += '<td class="column_name" data-column_name="sl" data-id="' + data[count].id + '">' + (count + 1) + '</td>';
                            html += '<td class="column_name" data-column_name="name" data-id="' + data[count].id + '">' + data[count].name + '</td>';
                            html += '<td class="column_name" data-column_name="slug" data-id="' + data[count].id + '">' + data[count].link + '</td>';
                            html += `<td class="column_name status" data-column_name="status" data-id="${data[count].id}">
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" id="${data[count].id}" ${data[count].status == 1 ? "checked" : ""} >
                                    <span class="switcher_control"></span>
                                </label>
                            </td>`;
                            html += '<td><a type="button" class="btn btn-primary btn-xs edit" id="' + data[count].id + '">{{translate('Edit')}}</a> </td></tr>';
                        }
                        $('tbody').html(html);
                    }
                }
            });
        }

        $('#add').on('click', function () {
            let name = $('#name').val();
            let link = $('#link').val();
            if (name == "") {
                toastr.error('{{translate('Social Name Is Requeired')}}.');
                return false;
            }
            if (link == "") {
                toastr.error('{{translate('Social Link Is Requeired')}}.');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.social-media-store')}}",
                method: 'POST',
                data: {
                    name: name,
                    link: link
                },
                success: function (response) {
                    if (response.error == 1) {
                        toastr.error('{{translate('Social Media Already taken')}}');
                    } else {
                        toastr.success('{{translate('Social Media inserted Successfully')}}.');
                    }
                    $('#name').val('');
                    $('#link').val('');
                    fetch_social_media();
                }
            });
        });
        $('#update').on('click', function () {
            $('#update').attr("disabled", true);
            let id = $('#id').val();
            let name = $('#name').val();
            let link = $('#link').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.social-media-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    name: name,
                    link: link,
                },
                success: function (data) {
                    $('#name').val('');
                    $('#link').val('');

                    toastr.success('{{translate('Social info updated Successfully')}}.');
                    $('#update').hide();
                    $('#add').show();
                    fetch_social_media();

                }
            });
            $('#save').hide();
        });
        $(document).on('click', '.delete', function () {
            let id = $(this).attr("id");
            if (confirm("{{translate('Are you sure delete this social media')}}?")) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('admin.business-settings.social-media-delete')}}",
                    method: 'POST',
                    data: {id: id},
                    success: function (data) {
                        fetch_social_media();
                        toastr.success('{{translate('Social media deleted Successfully')}}.');
                    }
                });
            }
        });
        $(document).on('click', '.edit', function () {
            $('#update').show();
            $('#add').hide();
            let id = $(this).attr("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.social-media-edit')}}",
                method: 'POST',
                data: {id: id},
                success: function (data) {
                    $(window).scrollTop(0);
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#link').val(data.link);
                    fetch_social_media()
                }
            });
        });
        $(document).on('change', '.status', function () {
            let id = $(this).data("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.social-media-status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{translate('Status updated successfully')}}');
                }
            });
        });
    </script>
@endpush
