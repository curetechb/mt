@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add Stock Out')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('StockOut Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('stockout.store') }}" method="POST">
            	@csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="vendor">{{translate('Vendor Name')}}</label>
                    <div class="col-sm-9">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="vendor" name="vendor" required>
                            <option value="">{{ translate('Select Vendor') }}</option>
                            @foreach (App\Models\Vendor::all() as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="product">{{translate('Product')}}</label>
                    <div class="col-sm-9">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="product" name="product" required>
                            <option value="">{{ translate('Select Product') }}</option>
                            @foreach (App\Models\Product::all() as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="quantity">{{translate('Quantity')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Quantity')}}" id="quantity" name="quantity" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="note">{{translate('Note')}}</label>
                    <div class="col-sm-9">
                        <textarea name="note" id="note" cols="30" rows="10" class="form-control" required></textarea>
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