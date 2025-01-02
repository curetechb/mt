@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
  <h5 class="mb-0 h6">{{translate('Add New Customer Cancel List')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0 h6">{{ translate('Customer Cancel List Information')}}</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('cancel_list.store') }}" method="POST">
        @csrf
        <div class="form-group row">
          <label class="col-sm-3 col-from-label" for="date">{{ translate("Date") }}</label>
          <div class="col-sm-9">
            <input type="date" placeholder="Date" id="date" name="date" class="form-control" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-from-label" for="total_order">{{translate("Total Order")}}</label>
          <div class="col-sm-9">
            <input type="text" placeholder="Total Order" id="total_order" name="total_order" class="form-control" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-from-label" for="cancel">{{translate("Cancel")}}</label>
          <div class="col-sm-9">
            <input type="text" placeholder="Cancel" id="cancel" name="cancel" class="form-control" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-from-label" for="delivery">{{translate("Delivery")}}</label>
          <div class="col-sm-9">
            <input type="text" placeholder="Delivery" id="delivery" name="delivery" class="form-control" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-from-label" for="nextday">{{translate("Next Day")}}</label>
          <div class="col-sm-9">
            <input type="text" placeholder="Next Day" id="nextday" name="nextday" class="form-control" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-from-label" for="processing">{{translate("Processing")}}</label>
          <div class="col-sm-9">
            <input type="text" placeholder="Processing" id="processing" name="processing" class="form-control" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-from-label" for="notes">{{translate("Notes")}}</label>
          <div class="col-sm-9">
            <textarea name="notes" id="notes" cols="30" rows="10" class="form-control"></textarea>
          </div>
        </div>

        <div class="form-group mb-0 text-right">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


