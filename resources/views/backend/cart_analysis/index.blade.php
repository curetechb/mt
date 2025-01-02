@extends('backend.layouts.app')

@section('content')

<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form id="sort_uploads" action="" class="d-flex">
                    <input type="text" class="form-control form-control-xs mr-2" name="q" placeholder="{{ translate('Search Customer') }}" value="{{ request('q') }}">
                    <button type="submit" class="btn btn-primary">{{ translate('Search') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">

  @foreach($users as $user)
  <div class="col-lg-6">
    <div class="card">
      <div class="text-center pt-3">
        <h5>{{ $user->phone }}</h5>
      </div>
      <hr>
      <div class="card-body">
        @foreach ($user->carts as $key => $cart)
        <div class="purchase-details">
          <!-- <div class="d-flex"> -->
          <div class="float-left">
            <img class="img-fluid lazyload mr-2" width="40" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($cart->product->thumbnail_img) }}" alt="{{ $cart->product->name  }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
          </div>
          <div class="">
            {{ $cart->product->name }}
          </div>
          <div class="d-flex justify-content-between">
            <p>{{ $cart->product->unit }}</p>
            <p>Qty. {{ $cart->quantity }}</p>
            <p>Tk.{{ $cart->price }}</p>
          </div>
        </div>
        @endforeach

      </div>
    </div>
  </div>
  @endforeach
</div>

{{ $users->links() }}

@endsection
