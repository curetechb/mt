@extends('backend.layouts.app')

@section('content')

@php
    ini_set('max_execution_time', 3000);
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All products')}} ({{ $products->total() }})</h1>
        </div>
        <div class="col text-right">


            {{-- <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Fetch Price')}}</span>
            </a> --}}

        </div>
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="{{ route("price.scrapper") }}" method="POST">
        @csrf

        <div class="card-header row gutters-5">
            <div class="d-flex align-items-center justify-content-between w-100">
                <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
                <div class="d-flex align-items-center">
                    <div class="form-group mb-0">
                        <select name="per_page" id="per_page" class="form-control" onchange="window.location.href = `{{route('price.scrapper')}}?per_page=${event.target.value}`">
                            <option value="">Per Page</option>
                            <option @if(request('per_page') == '50' ) selected @endif value="50">50</option>
                            <option @if(request('per_page') == '100' ) selected @endif value="100">100</option>
                            <option @if(request('per_page') == '200' ) selected @endif value="200">200</option>
                            <option @if(request('per_page') == '300' ) selected @endif value="300">300</option>
                            <option @if(request('per_page') == '400' ) selected @endif value="400">400</option>
                            <option @if(request('per_page') == '500' ) selected @endif value="500">500</option>
                        </select>
                    </div>
                    <div class="mx-2"></div>
                    <button type="submit" class="btn btn-circle btn-info">
                        <span>{{ translate('Update Price') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>{{translate('Name')}}</th>
                        <th>{{translate('MT Price')}}</th>
                        <th>{{translate('CD Price')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $key => $product)
                    <tr>

                        <td>
                            {{ $product->name }}
                        </td>

                        <td>
                            ৳{{ $product->unit_price }}
                        </td>
                        <td>
                            <input type="text" class="form-control" name="prices[{{ $product->id }}][]" class="cdprice" value="@php
                                    $externalURL = $product->clink;
                                    $client = new \Goutte\Client();

                                    $website = $client->request('GET', $externalURL);

                                    $website->filter('.discountedPrice')->each(function ($node) {
                                        if ($node->matches('.discountedPrice')) {
                                            echo $node->text();
                                        }
                                    });

                                    $website->filter('.price')->each(function ($node) {
                                        if ($node->matches('.price')) {
                                            echo $node->text();
                                        }
                                    });
                                @endphp">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection


@section('script')
    <script type="text/javascript">

    </script>
@endsection
