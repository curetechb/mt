@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
  <h5 class="mb-0 h6">{{translate('Edit Customer Complain')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0 h6">{{translate('Customer Complain Information')}}</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('complain.update', $complain->id) }}" method="POST">
        @csrf
        @method("PUT")
        <div class="d-flex row mb-5">
          <label class="col-lg-3 col-from-label" for="description">{{translate('order ID')}}</label>
          <div class="col-lg-9">
            <select class="form-control aiz-selectpicker" name="order_id" id="order_id"  data-live-search="true">
                <option value="">Select Order</option>
                <option value="00000" @if($complain->order_id == null) selected @endif>00000</option>
                @foreach($orders as $order)
                    <option @if($order->id == $complain->order_id) selected @endif value="{{ $order->id }}">{{ $order->code }}</option>
                @endforeach
            </select>
          </div>
        </div>

        <div class="d-flex row mb-5">
          <label class="col-lg-3 col-from-label" for="description">{{translate('Status')}}</label>
            <div class="col-lg-9">
            <select class="form-control aiz-selectpicker" name="status" id="status">
                <option value="pending" @if($complain->status == 'pending') selected @endif>{{translate('Pending')}}</option>
                <option value="solving" @if($complain->status == 'solving') selected @endif>{{translate('Solving')}}</option>
                <option value="solved" @if($complain->status == 'solved') selected @endif>{{translate('Solved')}}</option>
            </select>
            </div>
        </div>

        <div class="form-group row">
          <label class="col-lg-3 col-from-label" for="description">{{translate('Description')}}</label>
          <div class="col-lg-9">
            <input type="text" placeholder="{{translate('Description')}}" id="description" name="description" class="form-control" required value="{{ $complain->description }}">
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
