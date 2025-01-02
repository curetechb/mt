@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Edit Customer Feedback')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Customer Feedback Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('feedback.update', $feedback->id) }}" method="POST">
            	@csrf
              @method("PUT")
              <div class="d-flex row mb-5">
                    <label class="col-lg-3 col-from-label" for="description">{{translate('Customer')}}</label>
                    <div class="col-lg-9">
                        <select class="form-control aiz-selectpicker" name="user_id" id="user_id" required  data-live-search="true">
                            <option value="">Select Customer</option>
                            @foreach($users as $user)
                                <option @if($user->id == $feedback->user_id) selected @endif value="{{ $user->id }}">{{$user->phone}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="product">{{translate('Product')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Product')}}" id="product" name="product" value="{{ $feedback->product }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="price">{{translate('Price')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Price')}}" id="price" name="price" value="{{ $feedback->price }}"  class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="delivery">{{translate('Delivery')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Delivery')}}" id="delivery" name="delivery" value="{{ $feedback->delivery }}"  class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="note">{{translate('Note')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Note')}}" id="note" name="note" class="form-control" value="{{ $feedback->note }}" >
                    </div>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
