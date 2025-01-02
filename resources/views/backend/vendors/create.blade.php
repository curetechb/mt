@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Vendor')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Vendor Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('vendors.store') }}" method="POST">
            	@csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="company_name">{{translate('Company Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Company Name')}}" id="company_name" name="company_name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="contact_name">{{translate('Contact Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Contact Name')}}" id="contact_name" name="contact_name" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="contact_number">{{translate('Contact Number')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Contact Number')}}" id="contact_number" name="contact_number" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="contact_number">{{translate('Address')}}</label>
                    <div class="col-sm-9">
                        <textarea name="address" id="address" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
