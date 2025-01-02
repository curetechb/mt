@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Customer Feedback')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Customer Feedback Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf
                <div class="d-flex row mb-5">
                    <label class="col-lg-3 col-from-label" for="description">{{translate('Customer')}}</label>
                    <div class="col-lg-9">
                        <select class="form-control aiz-selectpicker" name="user_id" id="user_id" required  data-live-search="true">
                            <option value="">Select Customer</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{$user->phone}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="product">{{translate('Product')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Product')}}" id="product" name="product" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="price">{{translate('Price')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Price')}}" id="price" name="price" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="delivery">{{translate('Delivery')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Delivery')}}" id="delivery" name="delivery" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="note">{{translate('Note')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Note')}}" id="note" name="note" class="form-control" required>
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
