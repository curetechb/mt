@extends('backend.layouts.app')

@section('content')

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Delivery Boy Information')}}</h5>
        </div>

        <form action="{{ route('delivery-boys.update', $user->id) }}" method="POST">
            @csrf
            <input name="_method" type="hidden" value="PATCH">
            <div class="card-body">

				@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="phone">{{translate('Phone')}}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="phone" value="{{$user->phone}}" placeholder="Phone" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="password">
                        {{translate('New Password')}}
                    </label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" name="password" value="{{ old('password') }}" placeholder="New Password (Optional)" autocomplete="new-password">
                    </div>
                </div>

                <hr>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" value="{{$user->name}}" placeholder="Name" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="date_of_birth">{{translate('Date of Birth')}}</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" name="date_of_birth" value="{{$delivery_boy->dob}}" placeholder="Date of Birth" required>
                    </div>
                </div>
                {{-- <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="email">{{translate('Email')}}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="email" value="{{$user->email}}" placeholder="Email" required>
                    </div>
                </div> --}}


                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="emergency_contact_number">
                        {{translate('Emergency Contact Number')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="{{$delivery_boy->emergency_contact_number ?? ""}}" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" placeholder="Emergency Contact Number" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="signinSrEmail">
                        {{translate('NID Image')}}
                    </label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="nid_image" value="{{ $delivery_boy->nid_image ?? "" }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="nid">
                        {{translate('NID')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nid" value="{{ $delivery_boy->nid ?? "" }}" placeholder="NID" required>
                    </div>
                </div>




                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="signinSrEmail">
                        {{translate('NID Image Backpart')}}
                    </label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="nid_image_backpart" value="{{ $delivery_boy->nid_image_backpart ?? "" }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="password">{{translate('Password')}}</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                </div> --}}
                {{-- <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="country">
                        {{translate('Country')}}
                    </label>
                    <div class="col-sm-10">
                        <select class="form-control aiz-selectpicker" name="country_id" id="country_id" required>
                            <option value="">{{translate('Select Country')}}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @if($delivery_boy->country == $country->name) selected @endif>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-2">
                        <label>{{ translate('Area')}}</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control mb-3 aiz-selectpicker" name="area" id="edit_state"  data-live-search="true" required>
                            {{-- @foreach ($states as $key => $state)
                                <option value="{{ $state->id }}" @if($delivery_boy->state == $state->name) selected @endif>
                                    {{ $state->name }}
                                </option>
                            @endforeach --}}
                            <option value="">Select Area</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @if($area->id == $delivery_boy->city_id) selected @endif>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- <div class="row">
                    <div class="col-md-2">
                        <label>{{ translate('City')}}</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" >
                            @foreach ($cities as $key => $city)
                                <option value="{{ $city->id }}" @if($delivery_boy->city == $city->name) selected @endif>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="avatar">
                        {{translate('Image')}}
                    </label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="avatar" value="{{ $user->avatar }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label">{{translate('Address')}}</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="address">{{ $user->address }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label">{{translate('Vehicle Type')}}</label>
                    <div class="col-sm-10">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                              <input type="checkbox" name="cycle" class="form-check-input" value="cycle" @if($delivery_boy->has_cycle) checked @endif>Cycle
                            </label>
                          </div>
                          <div class="form-check-inline">
                            <label class="form-check-label">
                              <input type="checkbox" name="motor_bike" class="form-check-input" value="motor_bike" @if($delivery_boy->has_motorbike) checked @endif>Motor Bike
                            </label>
                          </div>
                          <div class="form-check-inline">
                            <label class="form-check-label">
                              <input type="checkbox" name="by_walk" class="form-check-input" value="by_walk" @if($delivery_boy->by_walk) checked @endif>By Walk
                            </label>
                          </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="registration_number">
                        {{translate('Registration Number')}}
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="{{ $delivery_boy->registration_number }}" name="registration_number" value="{{ old('registration_number') }}" placeholder="Registrtion Number" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="license">
                        {{translate('License')}}
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="license" value="{{ $delivery_boy->license ?? ""}}" placeholder="License" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="signinSrEmail">
                        {{translate('License Image')}}
                    </label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="license_image" value="{{ $delivery_boy->license_image }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="signinSrEmail">
                        {{translate('License Image Backpart')}}
                    </label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="license_image_backpart" value="{{ $delivery_boy->license_image_backpart }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

{{-- @section('script')
    <script type="text/javascript">

		(function($) {
			"use strict";
            $(document).on('change', '[name=country_id]', function() {
                var country_id = $(this).val();
                get_states(country_id);
            });

            $(document).on('change', '[name=state_id]', function() {
                var state_id = $(this).val();
                get_city(state_id);
            });

            function get_states(country_id) {
                $('[name="state_id"]').html("");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('get-state')}}",
                    type: 'POST',
                    data: {
                        country_id  : country_id
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if(obj != '') {
                            $('[name="state_id"]').html(obj);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    }
                });
            }

            function get_city(state_id) {
                $('[name="city_id"]').html("");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('get-city')}}",
                    type: 'POST',
                    data: {
                        state_id: state_id
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        if(obj != '') {
                            $('[name="city_id"]').html(obj);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    }
                });
            }
		})(jQuery);

    </script>
@endsection --}}
