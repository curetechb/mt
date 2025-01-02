@extends('backend.layouts.app')

@section('content')

<style>
    #map{
            width: 100%;
            height: 250px;
        }
</style>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Subscription Configuration')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group row">
                        <label class="col-sm-4 col-from-label">{{translate('Subscription Fee')}}</label>
                        <div class="col-sm-8">
                            <input type="hidden" name="types[]" value="subscription_fee">
                            <div class="input-group">
                                <input type="number" name="subscription_fee" class="form-control" value="{{ get_setting('subscription_fee') ? get_setting('subscription_fee') : "0" }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend">
                                        {{ \App\Models\Currency::find(get_setting('system_default_currency'))->code }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
    <script type="text/javascript">

    (function($) {
        "use strict";
        $(document).ready(function (){
            show_hide_div();
        })

        $("[name=delivery_boy_payment_type]").on("change", function (){
            show_hide_div();
        });

        function show_hide_div() {
            $("#salary_div").hide();
            $("#commission_div").hide();
            if($("[name=delivery_boy_payment_type]:checked").val() == 'salary'){
                $("#salary_div").show();
            }
            if($("[name=delivery_boy_payment_type]:checked").val() == 'commission'){
                $("#commission_div").show();
            }
        }
    })(jQuery);

    </script>

    @if (get_setting('google_map') == 1)

        @include('frontend.partials.google_map')

    @endif
@endsection
