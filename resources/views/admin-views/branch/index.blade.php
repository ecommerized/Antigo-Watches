@extends('layouts.admin.app')

@section('title', translate('Add new branch'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/branch.png')}}" alt="{{ translate('branch') }}">
                {{translate('add_new_branch')}}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="mb-0 d-flex gap-2 align-items-center">
                    <i class="tio-user"></i>
                    {{translate('Branch_Information')}}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('admin.branch.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('name')}}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ translate('New branch') }}" value="{{ old('name') }}" maxlength="255" required>
                            </div>

                            <div class="form-group">
                                <label class="input-label" for="">{{translate('address')}}</label>
                                <input type="text" name="address" class="form-control" placeholder="" value="{{ old('address') }}" required>
                            </div>
                        </div>

                        <div class="col-lg-6">

                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <label class="mb-0">{{translate('Branch_Image')}}</label>
                                    <small class="text-danger">* ( {{ translate('Ratio 1:1') }} )</small>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <div class="upload-file">
                                        <input type="file" name="image" id="customFileEg1"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}"
                                               class="upload-file__input">
                                        <div class="upload-file__img">
                                            <img width="150" id="viewer" src="{{asset('public/assets/admin/img/icons/upload_img.png')}}" alt="{{ translate('image') }}">
                                        </div>
                                    </div>
                                </div>
                                <p class="fs-14 text-muted mb-2 mt-0 text-center">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="input-label">{{translate('number')}}</label>
                                <input type="number" name="number" class="form-control" value="{{ old('number') }}"
                                       maxlength="255" placeholder="{{ translate('EX : +88 05454 6446') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="input-label">{{translate('email')}}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                       maxlength="255" placeholder="{{ translate('EX : example@example.com') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="input-label">{{translate('password')}}</label>
                                <input type="text" name="password" class="form-control" placeholder="{{ translate('Password') }}" maxlength="255" value="{{ old('password') }}" required>
                            </div>
                        </div>
                    </div>

                    @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                    @if($googleMapStatus == 1)
                        <h3 class="mt-5">{{translate('Branch_Location')}}</h3>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="input-label" for="">{{translate('latitude')}}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('Click on the map select your default location') }}">
                                        </i>
                                    </label>
                                    <input type="number" name="latitude" id="latitude" class="form-control" placeholder="{{ translate('Ex : -132.44442') }}"
                                           maxlength="255" value="{{ old('latitude') }}" step="any"
                                           required>
                                </div>

                                <div class="form-group">
                                    <label class="input-label" for="">{{translate('longitude')}}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('Click on the map select your default location') }}">
                                        </i>
                                    </label>
                                    <input type="number" name="longitude" id="longitude" class="form-control" placeholder="{{ translate('Ex : 94.233') }}"
                                           maxlength="255" value="{{ old('longitude') }}" step="any"
                                           required>
                                </div>

                                <div class="form-group">
                                    <label class="input-label" for="">
                                        {{translate('coverage (km)')}}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('This value is the radius from your restaurant location, and customer can order food inside  the circle calculated by this radius.') }}">
                                        </i>
                                    </label>
                                    <input type="number" name="coverage" min="1" max="1000" class="form-control"
                                           placeholder="{{ translate('Ex : 3') }}" value="{{ old('coverage') }}"
                                           required>
                                </div>
                            </div>

                            <div class="col-md-6" id="location_map_div">
                                <input id="pac-input" class="controls rounded" data-toggle="tooltip"
                                       data-placement="right"
                                       data-original-title="{{ translate('search_your_location_here') }}"
                                       type="text" placeholder="{{ translate('search_here') }}" />
                                <div id="location_map_canvas" class="overflow-hidden rounded h-100"></div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-3 mt-3">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@push('script_2')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()?->value }}&libraries=places&v=3.51"></script>
    <script src="{{ asset('public/assets/admin/js/image-upload.js') }}"></script>

    <script>
        'use strict';

        $( document ).ready(function() {
            function initAutocomplete() {
                let myLatLng = {

                    lat: 23.811842872190343,
                    lng: 90.356331
                };
                const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                    center: {
                        lat: 23.811842872190343,
                        lng: 90.356331
                    },
                    zoom: 13,
                    mapTypeId: "roadmap",
                });

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                });

                marker.setMap(map);
                var geocoder = geocoder = new google.maps.Geocoder();
                google.maps.event.addListener(map, 'click', function(mapsMouseEvent) {
                    var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                    var coordinates = JSON.parse(coordinates);
                    var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                    marker.setPosition(latlng);
                    map.panTo(latlng);

                    document.getElementById('latitude').value = coordinates['lat'];
                    document.getElementById('longitude').value = coordinates['lng'];


                    geocoder.geocode({
                        'latLng': latlng
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                document.getElementById('address').innerHtml = results[1].formatted_address;
                            }
                        }
                    });
                });
                const input = document.getElementById("pac-input");
                const searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
                });
                let markers = [];
                searchBox.addListener("places_changed", () => {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }
                    markers.forEach((marker) => {
                        marker.setMap(null);
                    });
                    markers = [];
                    const bounds = new google.maps.LatLngBounds();
                    places.forEach((place) => {
                        if (!place.geometry || !place.geometry.location) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        var mrkr = new google.maps.Marker({
                            map,
                            title: place.name,
                            position: place.geometry.location,
                        });
                        google.maps.event.addListener(mrkr, "click", function(event) {
                            document.getElementById('latitude').value = this.position.lat();
                            document.getElementById('longitude').value = this.position.lng();
                        });

                        markers.push(mrkr);

                        if (place.geometry.viewport) {
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            };
            initAutocomplete();
        });
    </script>


@endpush
