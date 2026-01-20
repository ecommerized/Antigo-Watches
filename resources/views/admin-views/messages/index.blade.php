@extends('layouts.admin.app')

@section('title', translate('Messages'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('/public/assets/admin/css/lightbox.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/message.png')}}"
                     alt="{{ translate('message') }}">
                {{translate('conversation_list')}}
            </h2>
        </div>

        <div class="row gy-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body px-2" id="conversation_sidebar">
                        <div class="chat_people media gap-3 mb-4 px-2">
                            <div class="avatar rounded-circle position-relative">
                                <img class="img-fit rounded-circle"
                                     src="{{auth('admin')->user()->image_fullpath}}"
                                     alt="{{ translate('image') }}">
                                <span class="avatar-status status-sm bg-success"></span>
                            </div>
                            <div class="chat_ib media-body">
                                <h5 class="mb-0">{{auth('admin')->user()->f_name}} {{auth('admin')->user()->l_name}}</h5>
                                <span class="fs-12">{{auth('admin')->user()->phone}}</span>
                            </div>
                        </div>

                        <div class="input-group mb-3 px-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-right-0 pr-0" id="basic-addon1"><i
                                            class="tio tio-search"></i></span>
                            </div>
                            <input type="text" class="cz-filter-search form-control border-left-0 pl-2 focus-none"
                                   placeholder="{{ translate('Search_user') }}" aria-label="Username"
                                   aria-describedby="basic-addon1" id="search-conversation-user" autocomplete="off">
                        </div>

                        <div class="customer-list-wrap">
                            @php($array=[])
                            @foreach($conversations as $conv)
                                @if(in_array($conv->user_id,$array)==false)
                                    @php(array_push($array,$conv->user_id))
                                    @php($user=\App\Models\User::find($conv->user_id))
                                    @php($unchecked=\App\Models\Conversation::where(['user_id'=>$conv->user_id,'checked'=>0])->count())

                                    @if(isset($user))
                                        <div class="sidebar_primary_div media gap-3 p-2 mb-2 align-items-center customer-list cursor-pointer view-conversation-message rounded {{$unchecked!=0?'conv-active':''}}"
                                             data-route="{{route('admin.message.view',[$conv->user_id])}}"
                                             data-id="customer-{{$conv->user_id}}"
                                             id="customer-{{$conv->user_id}}">
                                            <div class="avatar rounded-circle">
                                                <img class="img-fit rounded-circle"
                                                     src="{{$user['image_fullpath']}}"
                                                     alt="{{ translate('image') }}">
                                            </div>
                                            <h5 class="sidebar_name mb-0 d-flex gap-2 justify-content-between align-items-center flex-grow-1">
                                                <div>
                                                    <div>{{$user['f_name'].' '.$user['l_name']}}</div>
                                                    <a class="text-dark fs-12 font-weight-normal"
                                                       href="tel:{{ $user['phone'] }}">{{ $user['phone'] }}</a>
                                                </div>
                                                <span class="{{$unchecked!=0?'badge badge-soft-info badge-pill':''}}"
                                                      id="counter-{{$conv->user_id}}">{{$unchecked!=0?$unchecked:''}}</span>
                                            </h5>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8" id="view-conversation">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <h4 class="text-muted">{{translate('view Conversation')}}</h4>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";

        $("#search-conversation-user").on("keyup", function () {
            let input_value = this.value.toLowerCase().trim();

            let sidebar_primary_div = $(".sidebar_primary_div");
            let sidebar_name = $(".sidebar_name");

            for (let i = 0; i < sidebar_primary_div.length; i++) {
                const text_value = sidebar_name[i].innerText;
                if (text_value.toLowerCase().indexOf(input_value) > -1) {
                    sidebar_primary_div[i].style.display = "";
                } else {
                    sidebar_primary_div[i].style.setProperty("display", "none", "important");
                }
            }
        });

        let current_selected_user = null;

        $(".view-conversation-message").on('click', function () {
            let route = $(this).data('route');
            let id = $(this).data('id');
            viewConvs(route, id);
        });

        function viewConvs(url, id_to_active) {
            current_selected_user = id_to_active;

            let counter_element = $('#counter-' + current_selected_user.slice(9));
            let customer_element = $('#' + current_selected_user);
            if (counter_element !== "undefined") {
                counter_element.empty();
                counter_element.removeClass("badge");
                counter_element.removeClass("badge-info");
            }
            if (customer_element !== "undefined") {
                customer_element.removeClass("conv-active");
            }


            $('.customer-list').removeClass('conv-active');
            $('#' + id_to_active).addClass('conv-active');
            $.get({
                url: url,
                success: function (data) {
                    $('#view-conversation').html(data.view);
                }
            });
        }

        function replyConvs(url) {
            let form = document.querySelector('form');
            let formdata = new FormData(form);

            if (!formdata.get('reply') && !formdata.get('images[]')) {
                toastr.error('{{translate("Reply message is required!")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                return "false";
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                type: 'POST',
                data: formdata,
                processData: false,
                contentType: false,
                success: function (data) {
                    console.log(data.errors);
                    if (data.errors)
                    {
                        for (let i = 0; i < data.errors.length; i++)
                        {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{translate("Message sent")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        $('#view-conversation').html(data.view);
                    }
                },
                error() {
                    toastr.error('{{translate("Reply message is required!")}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function renderUserList() {
            $('#loading').show();
            $.ajax({
                url: "{{route('admin.message.get_conversations')}}",
                type: 'GET',
                cache: false,
                success: function (response) {
                    $('#loading').hide();
                    $("#conversation_sidebar").html(response.conversation_sidebar)

                },
                error: function (err) {
                    $('#loading').hide();
                }
            });
        }

    </script>

@endpush
