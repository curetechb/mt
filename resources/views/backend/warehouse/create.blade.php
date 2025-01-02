@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Warehouse')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Warehouse Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('warehouse.store') }}" method="POST">
            	@csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="address">{{translate('Address')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required>
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="state">{{translate('State')}}</label>
                    <div class="col-sm-9">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="product" name="product" >
                            <option value="">{{ translate('Select State') }}</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="areas">{{translate('Areas')}}</label>
                    <div class="col-sm-9">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="areas" name="areas[]" data-live-search="true" required multiple>
                            <option value="">{{ translate('Select Areas') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="latitude">{{translate('Latitude')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Latitude')}}" id="latitude" name="latitude" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="longitude">{{translate('Longitude')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Longitude')}}" id="longitude" name="longitude" class="form-control" required>
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="city_id">{{translate('City Id')}}</label>
                    <div class="col-sm-9">
                        <textarea name="address" id="address" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                </div> --}}

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
