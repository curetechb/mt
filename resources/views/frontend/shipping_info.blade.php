@extends('frontend.layouts.app')

@section('content')


<section class="mb-4 gry-bg">
    <div class="container">
        <div class="row cols-xs-space cols-sm-space cols-md-space">
            <div class="col-xxl-8 col-xl-10 pt-3">
                <form class="form-default checkout-form @if($subtotal > 0) d-block @else d-none @endif" data-toggle="validator" action="{{ route('orders.store') }}" role="form" method="POST">
                    @csrf

                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-header p-3">
                                <h3 class="fs-16 fw-600 mb-0">
                                    {{ translate('Delivery Address')}}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row gutters-5">
                                    @if(Auth::check())
                                    @foreach (Auth::user()->addresses as $key => $address)
                                        <div class="col-md-6 mb-3">
                                            <label class="aiz-megabox d-block bg-white mb-0 h-100">
                                                <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default)
                                                    checked
                                                @endif required>
                                                <span class="d-flex p-3 aiz-megabox-elem h-100">
                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-3 text-left">
                                                        <div>
                                                            <span class="opacity-60">{{ translate('Address') }}:</span>
                                                            <span class="fw-600 ml-2">{{ $address->address }}</span>
                                                        </div>

                                                        {{-- <div>
                                                            <span class="opacity-60">{{ translate('Postal Code') }}:</span>
                                                            <span class="fw-600 ml-2">{{ $address->postal_code }}</span>
                                                        </div> --}}
                                                        {{-- <div>
                                                            <span class="opacity-60">{{ translate('City') }}:</span>
                                                            <span class="fw-600 ml-2">{{ optional($address->city)->name }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="opacity-60">{{ translate('State') }}:</span>
                                                            <span class="fw-600 ml-2">{{ optional($address->state)->name }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="opacity-60">{{ translate('Country') }}:</span>
                                                            <span class="fw-600 ml-2">{{ optional($address->country)->name }}</span>
                                                        </div> --}}
                                                        <div>
                                                            <span class="opacity-60">{{ translate('Area') }}:</span>
                                                            <span class="fw-600 ml-2">{{ optional($address->state)->name }}</span>
                                                        </div>
                                                        @if ($address->floor_no)
                                                        <div>
                                                            <span class="opacity-60">{{ translate('Floor No') }}:</span>
                                                            <span class="fw-600 ml-2">{{ $address->floor_no }}</span>
                                                        </div>
                                                        @endif

                                                        @if ($address->apartment)
                                                        <div>
                                                            <span class="opacity-60">{{ translate('Apartment') }}:</span>
                                                            <span class="fw-600 ml-2">{{ $address->apartment }}</span>
                                                        </div>
                                                        @endif
                                                        <div>
                                                            <span class="opacity-60">{{ translate('Name') }}:</span>
                                                            <span class="fw-600 ml-2">{{ $address->name }}</span>
                                                        </div>
                                                        @if ($address->phone)
                                                        <div>
                                                            <span class="opacity-60">{{ translate('Phone') }}:</span>
                                                            <span class="fw-600 ml-2">{{ $address->phone }}</span>
                                                        </div>
                                                        @endif
                                                        <div class="invisible">Fix</div>
                                                    </span>
                                                </span>
                                            </label>
                                            <div class="dropdown position-absolute right-0 top-0">
                                                <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                                    <i class="la la-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" onclick="edit_address('{{$address->id}}')">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @endif
                                    <input type="hidden" name="checkout_type" value="logged">
                                    <div class="col-md-6 mx-auto mb-3" >
                                        <div class="border p-3 rounded mb-3 c-pointer text-center bg-white h-100 d-flex flex-column justify-content-center" onclick="add_new_address()">
                                            <i class="las la-plus la-2x mb-3"></i>
                                            <div class="alpha-7">{{ translate('Add New Address') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-header p-3">
                                <h3 class="fs-16 fw-600 mb-0">
                                    {{ translate('Preferred Delivery Time')}}
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div style="min-width: 300px">
                                        <div class="mb-3">
                                            <select name="delivery_date" id="delivery_date" class="form-control" required>
                                                @foreach ($delivery_dates as $day)
                                                    <option value="{{ $day['value'] }}">{{ $day['text'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <select name="delivery_time" id="delivery_times" class="form-control" required>
                                                @foreach ($delivery_times as $time)
                                                    <option value="{{ $time['value'] }}">{{ $time['text'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-header p-3">
                                <h3 class="fs-16 fw-600 mb-0">
                                    {{ translate('Select a payment option')}}
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                <div class="row">
                                    <div class="col-xxl-8 col-xl-10">
                                        <div class="row gutters-10">

                                            <div class="col-6 col-md-4">
                                                <label class="aiz-megabox d-block mb-3">
                                                    <input value="cash_on_delivery" class="online_payment" type="radio" name="payment_option" checked>
                                                    <span class="d-block p-3 aiz-megabox-elem">
                                                        <img src="{{ static_asset('assets/img/cards/cod.png')}}" class="img-fluid mb-2">
                                                        <span class="d-block text-center">
                                                            <span class="d-block fw-600 fs-15">{{ translate('Cash on Delivery')}}</span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>

                                            @if(get_setting('bkash') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="bkash" class="online_payment" type="radio" name="payment_option">
                                                        <span class="d-block p-3 aiz-megabox-elem">
                                                            <img src="{{ static_asset('assets/img/cards/bkash.png')}}" class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span class="d-block fw-600 fs-15">{{ translate('Bkash')}}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                    {{-- <div class="container">
                        <div class="pt-3">
                            <label class="aiz-checkbox">
                                <input type="checkbox" required id="agree_checkbox">
                                <span class="aiz-square-check"></span>
                                <span>{{ translate('I agree to ')}}</span>
                                <span class="fw-600 fst-italic" style="font-style: italic">Muslim Town </span>
                            </label>
                            <a href="{{ route('terms-conditions') }}"> terms</a>
                        </div>
                    </div> --}}

                    @if (Auth::check() && get_setting('coupon_system') == 1)
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="mt-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="code" onkeydown="return event.key != 'Enter';"
                                            placeholder="{{ translate('Discount Code') }}" id="coupon_code">
                                        <div class="input-group-append">
                                            <button type="button" id="apply-coupon-btn"
                                                class="btn btn-primary">{{ translate('Apply') }}</button>
                                            <button type="button" id="coupon-remove-btn" style="display: none"
                                                class="btn btn-danger">{{ translate('Remove Coupon') }}</button>
                                        </div>
                                    </div>
                                    <div class="text-danger" id="coupon_error"></div>
                                    <div class="text-success" id="coupon_success"></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end checkout-btn-row">
                        <div>
                            <div class="text-right mb-2">
                                <small style="font-size: 14px"><span class="shipping_cost">{{ single_price($shipping_cost) }}</span> Delivery Charge Included</small>
                            </div>
                            <div class="checkout-proceed-btn-container">
                                <button type="submit" class="btn btn-primary w-100 fw-600 checkout-proceed-btn">
                                    <span>Proceed</span>
                                    <span class="ml-auto checkout-cart-total">{{ single_price($total) }}</span>
                                </button>
                                <a href="#" class="text-reset d-flex align-items-center justify-content-center bg-white px-3 open-cart">
                                    <i class="las la-shopping-cart fs-22 opacity-60"></i>
                                    @php
                                        $count = (isset($carts) && count($carts)) ? count($carts) : 0;
                                    @endphp
                                    <span class="badge bg-primary ml-1 fw-600 cart-count">{{$count}}</span>
                                </a>
                            </div>
                            <div>
                                <small>By clicking/tapping proceed, I agree to Muslim Town's <a href="{{ route('terms-conditions') }}">Terms of Services</a></small>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="text-center checkout-cart-empty mt-5 @if($subtotal > 0) d-none @endif">
                    <i class="las la-shopping-bag la-5x text-primary" style="background: #eee;
                    border-radius: 50%;
                    padding: 25px;"></i>
                    <h1 class="fs-20 py-3 text-center">Your shopping bag is empty, Please add some products before you checkout.</h1>
                    <a href="{{ url("/") }}" class="btn btn-primary px-3">Start Shopping Now</a>
                </div>
            </div>
        </div>
    </div>
</section>




{{-- Address Modal --}}
<div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3">

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Address')}}</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control mb-3" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Area')}}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control aiz-selectpicker mb-3" data-live-search="true" data-placeholder="{{ translate('Select your Area') }}" name="area" required>
                                    <option value="">{{ translate('Select your Area') }}</option>
                                    @foreach (\App\Models\State::where('status', 1)->get() as $key => $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Floor No')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="{{ translate('Floor No')}}" name="floor_no" value="">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Apartment')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="{{ translate('Apartment')}}" name="apartment" value="">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Name')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="{{ translate('Name')}}" name="name" value="" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Alternative Phone')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="{{ translate('+880')}}" name="phone" value="">
                            </div>
                        </div>

                        {{-- <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Country')}}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                        <option value="">{{ translate('Select your country') }}</option>
                                        @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('State')}}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_id" required>

                                </select>
                            </div>
                        </div> --}}

                        {{-- <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('City')}}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required>

                                </select>
                            </div>
                        </div> --}}

                        @if (get_setting('google_map') == 1)
                            <div class="row">
                                <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
                                <div id="map"></div>
                                <ul id="geoData">
                                    <li style="display: none;">Full Address: <span id="location"></span></li>
                                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                                    <li style="display: none;">Country: <span id="country"></span></li>
                                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">Longitude</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3" id="longitude" name="longitude" readonly="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">Latitude</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3" id="latitude" name="latitude" readonly="">
                                </div>
                            </div>
                        @endif



                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-sm btn-primary">OK</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Edit Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="edit_modal_body">

            </div>
        </div>
    </div>
</div>




@endsection



@section('script')


    <script>
        $("#delivery_date").on("change", function(e){

            const delivery_date = e.target.value;

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('delivery_slots') }}?delivery_date="+delivery_date,
                type: 'GET',
                success: function (response) {
                    $("#delivery_times").html(response);
                }
            });

        });
    </script>


    <script type="text/javascript">
        function add_new_address(){
            $('#new-address-modal').modal('show');
        }

        function edit_address(address) {
            var url = '{{ route("addresses.edit", ":id") }}';
            url = url.replace(':id', address);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#edit_modal_body').html(response.html);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');

                    @if (get_setting('google_map') == 1)
                        var lat     = -33.8688;
                        var long    = 151.2195;

                        if(response.data.address_data.latitude && response.data.address_data.longitude) {
                            lat     = response.data.address_data.latitude;
                            long    = response.data.address_data.longitude;
                        }

                        initialize(lat, long, 'edit_');
                    @endif
                }
            });
        }

        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });

        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }


    </script>

    <script>
        $(document).on("click", "#apply-coupon-btn", function() {

            $("#coupon_error").html("");

            let code = $("#coupon_code").val();

            var formData = new FormData();
            formData.append("code", code);
            // formData.append("_token", "{{ csrf_token() }}");
            // var data = new FormData($('#apply-coupon-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('checkout.apply.coupon') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {

                    $("#coupon_error").html("");
                    $("#coupon_success").html("Discount "+data.coupon_discount+" applied");
                    $(".checkout-cart-total").html(data.total);
                    $("#coupon-remove-btn").show();
                    $("#apply-coupon-btn").hide();

                    $("#coupon_code").attr("readonly", "readonly");
                    // AIZ.plugins.notify(data.response_message.response, data.response_message.message);
                    //                    console.log(data.response_message);
                    // $("#cart_summary").html(data.html);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(jqXHR.responseJSON.message)
                        $("#coupon_error").html(jqXHR.responseJSON.message);
                        $("#coupon_success").html("");
                }
            })
        });

        $(document).on("click", "#coupon-remove-btn", function() {
            // var data = new FormData($('#remove-coupon-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('checkout.remove.coupon') }}",
                data: {},
                cache: false,
                contentType: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {
                    $(".checkout-cart-total").html(data.total);

                    $("#coupon-remove-btn").hide();
                    $("#apply-coupon-btn").show();
                    $("#coupon_code").removeAttr("readonly");
                    $("#coupon_code").val("");

                    $("#coupon_error").html("");
                    $("#coupon_success").html("");

                }
            })
        });

    </script>


    @if (get_setting('google_map') == 1)
        @include('frontend.partials.google_map')
    @endif
@endsection
