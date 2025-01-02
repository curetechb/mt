@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="card">
        <form action="{{ route("vendor.products.add") }}" method="post" class="card-body p-2">
            @csrf
            <div class="row align-items-center">
                <div class="col-md-6">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="product" name="product" required>
                        <option value="">{{ translate('Select Product') }}</option>
                        @foreach (App\Models\Product::all() as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="vendor_id" value="{{ request("id") }}">
                    <button class="btn btn-primary" type="submit">{{ translate('Add Product to '. $vendor->company_name) }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate($vendor->company_name.' Products')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg" width="10%">#</th>
                    <th>{{ translate('Info') }}</th>
                    <th data-breakpoints="lg">{{ translate('Total Stock') }}</th>
                    <th width="10%">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{translate('Num of Sale')}}:</strong> {{ $product->num_of_sale }} {{translate('times')}} <br/>
                            <strong>{{translate('Base Price')}}:</strong> {{ single_price($product->unit_price) }} <br/>
                            <strong>{{translate('Rating')}}:</strong> {{ $product->rating }} <br/>
                        </td>
                        <td class="text-right">
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('vendor.products.delete', ['id' => $product->id, 'vendor_id' => request('id')])}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $products->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
