@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center pt-4">
                                <h1 class="h4 fw-600">
                                    {{ translate('Enter your mobile number')}}
                                </h1>
                                <!--<div class="px-4">{{ translate('Please verify your phone') }}</div>-->
                            </div>

                            <div class="px-4 py-3 py-lg-4">
                                <div class="">
                                    <form class="form-default" role="form" action="{{ route('login') }}" method="POST" id="phone-login">
                                        @csrf

                                        <div class="input-group phone-form-group mb-3">
                                            <span class="input-group-text px-2" style="border-radius: 4px 0 0 4px;">
                                                <img src="{{ static_asset("assets/img/bangladesh.png") }}" alt="">
                                                <span class="mx-1"></span>
                                                <span>+88</span>
                                            </span>
                                            <input type="hidden" name="country_code" value="88">
                                            <input type="text" value="{{ old('phone') }}" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="" id="phone" name="phone"  autocomplete="off">
                                            @if ($errors->has('phone'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('phone') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group" id="otpblock" style="display: none">
                                            <input type="text" class="form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}" value="{{ old('otp') }}" placeholder="{{  translate('OTP') }}" name="otp" id="otp" autocomplete="off">
                                            {{-- <button type="submit" class="resent-btn">Resent Verification Code</button> --}}
                                        </div>
                                        <div class="text text-danger mb-3" id="showerror">

                                        </div>
                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-primary btn-block fw-600" id="sendcodebtn">{{  translate('Continue') }}</button>
                                        </div>

                                    </form>

                                    {{-- @if (env("DEMO_MODE") == "On")
                                        <div class="mb-5">
                                            <table class="table table-bordered mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td>{{ translate('Seller Account')}}</td>
                                                        <td>
                                                            <button class="btn btn-info btn-sm" onclick="autoFillSeller()">{{ translate('Copy credentials') }}</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ translate('Customer Account')}}</td>
                                                        <td>
                                                            <button class="btn btn-info btn-sm" onclick="autoFillCustomer()">{{ translate('Copy credentials') }}</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ translate('Delivery Boy Account')}}</td>
                                                        <td>
                                                            <button class="btn btn-info btn-sm" onclick="autoFillDeliveryBoy()">{{ translate('Copy credentials') }}</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    @if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1)
                                        <div class="separator mb-3">
                                            <span class="bg-white px-3 opacity-60">{{ translate('Or Login With')}}</span>
                                        </div>
                                        <ul class="list-inline social colored text-center mb-5">
                                            @if (get_setting('facebook_login') == 1)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                                        <i class="lab la-facebook-f"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(get_setting('google_login') == 1)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                                        <i class="lab la-google"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if (get_setting('twitter_login') == 1)
                                                <li class="list-inline-item">
                                                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
                                                        <i class="lab la-twitter"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif --}}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if(country.iso2 == 'bd'){
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            onlyCountries: @php echo json_encode(\App\Models\Country::where('status', 1)->pluck('code')->toArray()) @endphp,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if(selectedCountryData.iso2 == 'bd'){
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function(e) {
            // var currentMask = e.currentTarget.placeholder;

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });

        function toggleEmailPhone(el){
            if(isPhoneShown){
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                $('input[name=phone]').val(null);
                isPhoneShown = false;
                $(el).html('{{ translate('Use Phone Instead') }}');
            }
            else{
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                $('input[name=email]').val(null);
                isPhoneShown = true;
                $(el).html('{{ translate('Use Email Instead') }}');
            }
        }



        function autoFillSeller(){
            $('#email').val('seller@example.com');
            $('#password').val('123456');
        }
        function autoFillCustomer(){
            $('#email').val('customer@example.com');
            $('#password').val('123456');
        }
        function autoFillDeliveryBoy(){
            $('#email').val('deliveryboy@example.com');
            $('#password').val('123456');
        }
    </script>

    <script>
        $(document).ready(function(){

            $("#phone-login").on("submit", function(e){
                e.preventDefault();

                let phone = $("#phone").val();
                let otp = $("#otp").val();
                $("#sendcodebtn").attr("disabled", true);

                $.ajax({
                    url: "{{ route('phone.verifiy') }}",
                    type: "post",
                    data: {"_token": "{{ csrf_token() }}", phone: phone, otp: otp} ,
                    success: function (response) {

                        // $("#sendcodebtn").attr("disabled", false);
                        $("#showerror").html("");
                        console.log(response);

                        if(response.otpsent){
                            $("#sendcodebtn").attr("disabled", false);
                            $("#otpblock").css("display", "block");
                            $("#sendcodebtn").html("Verify");
                        }

                        if(response.verified){
                            localStorage.setItem("phone", phone);
                            localStorage.setItem("code", otp);
                            window.location.href = "{{ route('checkout.shipping_info') }}";
                        }

                    // You will get response from your PHP page (what you echo or print)
                    },
                    error: function(err) {
                        $("#sendcodebtn").attr("disabled", false);
                        if(err.status == 422){
                            $("#showerror").html(err.responseJSON.errors.phone[0]);
                        }
                        console.log(err);
                    }
                });

            });

        });
    </script>
@endsection
