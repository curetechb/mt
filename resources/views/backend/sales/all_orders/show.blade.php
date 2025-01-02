@extends('backend.layouts.app')

@push('head_tags')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
    </style>
@endpush

@section('content')

@php
    $payment_details = json_decode($order->payment_details);
    $refund_details = json_decode($order->refund_details);
    $shipping_details = json_decode($order->shipping_address);
@endphp

    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
        </div>
        <div class="card-body">
            @php
                $delivery_status = $order->delivery_status;
                $payment_status = $order->payment_status;
            @endphp

            <div class="row gutters-5 mb-3">
                <div class="col text-center text-md-left">
                </div>

                {{-- @if (Auth::user()->user_type == 'admin' || Auth::user()->staff->role->id != 4)
                    <div class="col">
                        <label for="manage_by">{{ translate('Manage By') }}</label>
                        <select class="form-control" aria-label="Default select example" id="manage_by" name="manage_by">
                            <option>Order Managed By?</option>
                            <option @if ($order->manage_by == 'sonar') selected @endif value="sonar">Sonar Courier</option>
                        </select>
                    </div>
                @endif --}}

                <!--Assign Delivery Boy-->
                <div class="col-md-3 ml-auto">
                    <label for="assign_deliver_boy">{{ translate('Assign Deliver Boy') }}</label>
                    @if ($delivery_status == 'pending' || $delivery_status == 'processing')
                        <select class="form-control aiz-selectpicker" data-live-search="true"
                            data-minimum-results-for-search="Infinity" id="assign_deliver_boy">
                            <option value="">{{ translate('Select Delivery Boy') }}</option>
                            @foreach ($delivery_boys as $delivery_boy)
                                <option value="{{ $delivery_boy->id }}"
                                    @if ($order->assign_delivery_boy == $delivery_boy->id) selected @endif>
                                    {{ $delivery_boy->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ optional($order->delivery_boy)->name }}" disabled>
                    @endif
                </div>

                @if (Auth::user()->user_type == 'admin' || Auth::user()->staff->role->id != 4)
                    <div class="col ml-auto">
                        <label for="update_payment_status">{{ translate('Payment Status') }}</label>
                        <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                            id="update_payment_status">
                            <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>
                                {{ translate('Unpaid') }}</option>
                            <option value="paid" @if ($payment_status == 'paid') selected @endif>{{ translate('Paid') }}</option>
                            <option value="partially_paid" @if ($payment_status == 'partially_paid') selected @endif>{{ translate('Partially Paid') }}</option>
                                <option value="refunded" @if ($payment_status == 'refunded') selected @endif>{{ translate('Refunded') }}
                            <option value="emergency" @if ($payment_status == 'emergency') selected @endif>{{ translate('Paid with Emergency Balance') }}
                            </option>
                        </select>
                    </div>
                @endif

                <div class="col ml-auto">
                    <label for="update_delivery_status">{{ translate('Delivery Status') }}</label>
                    @if ($delivery_status != 'delivered' && $delivery_status != 'cancelled')
                        <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                            id="update_delivery_status">
                            <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                                {{ translate('Pending') }}</option>
                            <option value="processing" @if ($delivery_status == 'processing') selected @endif>
                                {{ translate('Processing') }}</option>
                            {{-- <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{translate('Confirmed')}}</option> --}}
                            {{-- <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>{{translate('Picked Up')}}</option> --}}
                            <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>
                                {{ translate('On The Way') }}</option>
                            <option value="next_day" @if ($delivery_status == 'next_day') selected @endif>
                                {{ translate('Next Day') }}</option>
                            <option value="payment_received" @if ($delivery_status == 'payment_received') selected @endif>
                                    {{ translate('Payment Received') }}</option>
                            <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                {{ translate('Delivered') }}</option>
                            {{-- <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>{{translate('Cancelled')}}</option> --}}
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ $delivery_status }}" disabled>
                    @endif
                </div>

                <!-- Button trigger modal -->
                @if ($order->delivery_status != 'cancelled' && $order->delivery_status != 'delivered')
                    <div class="col ml-auto mt-3">
                        <div>
                            <button type="button" class="btn btn-primary" style="margin-top: 10px" data-toggle="modal"
                                data-target="#exampleModal">
                                {{ translate('Cancel') }}
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Modal -->

                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('all_orders.cancel', $order->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Cancel Reason</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <textarea class="form-control" name="reason" id="" cols="30" rows="5" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>


        <div class="row gutters-5">
            <div class="col-md-4 text-center text-md-left">
                <address>

                    <strong class="text-main">{{ $shipping_details->name ?? '' }}</strong><br>
                    {{ $order->user->phone ?? '' }}<br>
                    {{ $shipping_details->phone ?? '' }}<br>
                    {{ $shipping_details->address ?? '' }},
                    
                </address>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4 ml-auto">
                <table>
                    <tbody>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Order #') }}</td>
                            <td class="text-right text-info text-bold"> {{ $order->code }}</td>
                        </tr>
                        @if ($order->invoice_number)
                            <tr>
                                <td class="text-main text-bold">{{ translate('Invoice Number #') }}</td>
                                <td class="text-right text-info text-bold"> {{ $order->invoice_number }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-main text-bold">{{ translate('Order From') }}</td>
                            <td class="text-right">
                               {{ $order->ordered_from }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Order Status') }}</td>
                            <td class="text-right">
                                @if ($delivery_status == 'delivered')
                                    <span
                                        class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @elseif($delivery_status == 'pending')
                                    <span
                                        class="badge badge-inline badge-warning">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @elseif($delivery_status == 'picked_up')
                                    <span
                                        class="badge badge-inline badge-secondary">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @elseif($delivery_status == 'on_the_way')
                                    <span
                                        class="badge badge-inline badge-primary">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @elseif($delivery_status == 'cancelled')
                                    <span class="badge badge-inline badge-danger" data-toggle="tooltip"
                                        data-placement="top"
                                        title="{{ $order->reason }}">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @else
                                    <span
                                        class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Order Date') }} </td>
                            <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{ translate('Total amount') }}
                            </td>
                            <td class="text-right">
                                {{ single_price($order->grand_total) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Payment method') }}</td>
                            <td class="text-right">
                                <a href="#" id="payment_details" data-toggle="modal"
                                            data-target="#paymentdetailsmodal">
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}
                                </a>
                                @if ($order->payment_type == "bkash" && $order->payment_status == "paid" && $order->payment_details)
                                <div class="modal fade" id="paymentdetailsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Payment Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="d-flex">
                                                            <strong class="mr-1">Invoice Number: </strong>
                                                            <p>{{ "#".$payment_details->merchantInvoiceNumber }}</p>
                                                        </div>
                                                        <div class="d-flex">
                                                            <strong class="mr-1">Customer Number: </strong>
                                                            <p>{{ $payment_details->customerMsisdn }}</p>
                                                        </div>
                                                        <div class="d-flex">
                                                            <strong class="mr-1">Amount: </strong>
                                                            <p>BDT {{ $payment_details->amount }}</p>
                                                        </div>
                                                        <div class="d-flex">
                                                            <strong class="mr-1">Payment ID: </strong>
                                                            <p> {{ $payment_details->paymentID }}</p>
                                                        </div>
                                                        <div class="d-flex">
                                                            <strong class="mr-1">Transaction ID: </strong>
                                                            <p> {{ $payment_details->trxID }}</p>
                                                        </div>
                                                        {{--<div class="d-flex">
                                                            <strong class="mr-1">Transaction Time: </strong>
                                                            <p> {{ date('d-M-Y h:iA', strtotime($payment_details->createTime)) }}</p>
                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{ translate('Payment Status') }}</td>
                            <td class="text-right">
                                {{ translate(ucfirst(str_replace('_', ' ', $order->payment_status))) }}

                            </td>
                        </tr>



                        <tr>
                            <td class="text-main text-bold">{{ translate('Refund Status') }}</td>
                            <td class="text-right">
                                @if ($order->refund_details != null)
                                    <a href="#" id="refund_details" data-toggle="modal"
                                            data-target="#refunddetailsmodal">
                                        Refunded
                                    </a>

                                    <div class="modal fade" id="refunddetailsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Refund Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="bkashrefundcontentloader" class="text-center p-3">
                                                            <i class="las la-spinner la-spin la-3x"></i>
                                                        </div>
                                                        <div id="bkashrefundcontent" style="display: none">
                                                            <div class="d-flex">
                                                                <strong class="mr-1">Amount: </strong>
                                                                <p>BDT {{ $refund_details->amount }}</p>
                                                            </div>
                                                            {{-- <div class="d-flex">
                                                                <strong class="mr-1">Charge: </strong>
                                                                <p> {{ $refund_details->charge }}</p>
                                                            </div> --}}
                                                            <div class="d-flex">
                                                                <strong class="mr-1">Original Transaction ID: </strong>
                                                                <p> {{ $refund_details->originalTrxID }}</p>
                                                            </div>
                                                            <div class="d-flex">
                                                                <strong class="mr-1">Refund Transaction ID: </strong>
                                                                <p> {{ $refund_details->refundTrxID }}</p>
                                                            </div>
                                                            {{--<div class="d-flex">
                                                                <strong class="mr-1">Transaction Time: </strong>
                                                                <p> {{ date('d-M-Y h:iA', strtotime($refund_details->completedTime)) }}</p>
                                                            </div>--}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <div class="d-flex justify-content-center align-items-center">
                                    @if (get_setting('bkash') == 1 && $order->payment_status == "paid" && $order->payment_type == "bkash" && $order->refund_details == null)
                                        <div class="rounded text-center p-2 d-flex border mt-2" role="button" id="bkash_refund" data-id="{{ $order->id }}" data-toggle="modal"
                                            data-target="#bkashrefundmodal">
                                            <img src="{{ static_asset('assets/img/cards/bkash.png')}}" width="40" class="img-fluid">
                                            <div class="d-block text-center ml-2">
                                                <span class="d-block fw-600 fs-15">{{ translate('Bkash Refund')}}</span>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="bkashrefundmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">

                                                    <form action="{{ route('bkash.refund') }}" method="POST" id="bkashrefundform">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Refund Reason</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="">Amount</label>
                                                                <input type="number" name="amount" class="form-control" id="refund_amount" value="{{ $order->grand_total }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Reason</label>
                                                                <textarea class="form-control" name="reason" id="refund_reason" cols="30" rows="5" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                            <button type="submit" class="btn btn-info">Submit</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @endif
                                </div>
                            </td>
                        </tr>

                        @if ($order->preferred_delivery_date && $order->delivery_slot)
                            <tr>
                                <td class="text-main text-bold">{{ translate('Preferred Delivery Time') }}: </td>
                                <td class="text-right fw-600 pl-3">
                                    @php
                                        $times = [
                                            "9:00AM - 12:00PM",
                                            "12:00PM - 3:00PM",
                                            "3:00PM - 6:00PM",
                                            "6:00PM - 9:00PM",
                                        ];
                                    @endphp
                                    {{ date("d-M-Y", strtotime($order->preferred_delivery_date)) }} - ({{ $times[$order->delivery_slot - 1] }})
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>


     <form action="{{ route('customer.add_product')}}" method="POST">
        @csrf
        <div class="row">

            <div class="col-md-4">
                <label for="product_list">{{ translate('Product List')}}</label>
                <select class="js-data-example-ajax" name="product_id" required style="width: 100%"></select>
            </div>


            <div class="col-md-2">
                <label class="col-for-label" for="quantity">{{ translate('Quantity')}}</label>
                <input type="text" class="form-control" id="quantity" name="quantity" placeholder="{{ translate('Quantity')}}" required>
            </div>

            <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">

            <!-- Button trigger modal -->
                <div class="col ml-auto mt-3">
                    <div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 10px" >
                            {{ translate('Add') }}
                        </button>
                    </div>
                </div>
        </div>
      </form>



        <hr class="new-section-sm bord-no">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table class="table table-bordered aiz-table invoice-summary">
                    <thead>
                        <tr class="bg-trans-dark">
                            <th data-breakpoints="lg" class="min-col">#</th>
                            <th width="10%">{{ translate('Photo') }}</th>
                            <th class="text-uppercase">{{ translate('Description') }}</th>
                            <!--<th data-breakpoints="lg" class="text-uppercase">{{ translate('Delivery Type') }}</th>-->
                            <th data-breakpoints="lg" class="min-col text-center text-uppercase">{{ translate('Qty') }}
                            </th>
                            <th data-breakpoints="lg" class="min-col text-center text-uppercase">{{ translate('Price') }}
                            </th>
                            <th data-breakpoints="lg" class="min-col text-center text-uppercase">{{ translate('Total') }}
                            </th>
                            @if (count($order->orderDetails) > 1)
                            <th data-breakpoints="lg" class="min-col text-left text-uppercase">{{ translate('Options') }}
                            </th>
                            @endif

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $key => $orderDetail)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                        <a href="{{ route('product', $orderDetail->product->slug) }}"
                                            target="_blank"><img height="50"
                                                src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></a>
                                    @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                        <a href="{{ route('auction-product', $orderDetail->product->slug) }}"
                                            target="_blank"><img height="50"
                                                src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></a>
                                    @else
                                        <strong>{{ translate('N/A') }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                        <strong><a href="{{ route('product', $orderDetail->product->slug) }}"
                                                target="_blank" class="text-muted">
                                                {{ $orderDetail->product->getTranslation('name') }}
                                                {{ $orderDetail->product->unit_value > 0 ? $orderDetail->product->unit_value : '' }}
                                                {{ $orderDetail->product->unit ?? '' }}
                                            </a></strong>
                                        <small>{{ $orderDetail->variation }}</small>
                                    @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                        <strong><a href="{{ route('auction-product', $orderDetail->product->slug) }}"
                                                target="_blank"
                                                class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</a></strong>
                                    @else
                                        <strong>{{ translate('Product Unavailable') }}</strong>
                                    @endif
                                </td>

                                <td class="text-center">{{ $orderDetail->quantity }}</td>
                                <td class="text-center">{{ single_price($orderDetail->price / $orderDetail->quantity) }}
                                </td>
                                <td class="text-center">{{ single_price($orderDetail->price) }}</td>
                                @if (count($order->orderDetails) > 1)
                                    <td>
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{ route('destroy.orderdetails', $orderDetail->id) }}" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="clearfix float-right">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Sub Total') }} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->orderDetails->sum('price')) }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Shipping') }} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->shipping_cost) }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Coupon Discount') }} :</strong>
                        </td>
                        <td>
                            <span class="text-danger">-{{ single_price($order->coupon_discount) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Reward Discount') }} :</strong>
                        </td>
                        <td>
                            <span class="text-danger">-{{ single_price($order->reward_discount) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('TOTAL') }} :</strong>
                        </td>
                        <td class="text-muted h5">
                            {{ single_price($order->grand_total - $order->coupon_discount - $order->reward_discount) }}
                        </td>
                    </tr>
                    @php
                        $emergency_order = \App\Models\Order::where("user_id", $order->user_id)->where("id", "!=", $order->id)->where("is_emergency_order", true)->where("payment_status", "!=", "paid")->first();
                    @endphp
                    @if ($emergency_order)
                        @php
                            $order_total = $order->grand_total - $order->coupon_discount - $order->reward_discount;
                            $previous_due = $emergency_order->grand_total - $emergency_order->coupon_discount - $emergency_order->reward_discount;
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Previous Due') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                {{ single_price($previous_due) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Total with Due') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                {{ single_price($order_total + $previous_due) }}
                            </td>
                        </tr>
                    @endif
                    @if ($order->is_b2b_order)
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Paid') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                {{ single_price($order->paid_amount) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Due') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                {{ single_price($order->due_amount) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

             @if ($order->is_b2b_order)
                 <!-- Button trigger modal -->
                <div class="mb-3">
                    <button type="button" class="btn btn-primary btn-block" style="margin-top: 10px" data-toggle="modal"
                        data-target="#paymentModal">
                        {{ translate('Add Payment') }}
                    </button>
                </div>

                <!-- Modal -->

                <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('newsale.payment', $order->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="paymentModalLabel">Payment Amount</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <input type="number" class="form-control" name="amount" id="amount" required placeholder="amount"/>
                                    </div>
                                    <div class="mb-3">
                                        <input type="datetime-local" class="form-control" name="payment_time" id="payment_time" required/>
                                    </div>
                                    <div class="mb-3">
                                        <textarea name="notes" id="notes" cols="30" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
             @endif


            <div class="text-right no-print">
                <a href="{{ route('invoice.download', ['order_id' => $order->id, 'language' => 'english']) }}" type="button" class="btn btn-icon btn-light" data-toggle="tooltip"
                                        data-placement="top" title="English"><i
                        class="las la-print"></i></a>
                <a href="{{ route('invoice.download', ['order_id' => $order->id, 'language' => 'bengali']) }}" type="button" class="btn btn-icon btn-light" data-toggle="tooltip"
                                        data-placement="top" title="Bengali"><i
                class="las la-print"></i></a>
            </div>
        </div>

        @if (Auth::user()->user_type == 'admin')
            <form action="{{ route('discount.give', $order->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-auto">
                        <div class="form-group mb-0">
                        </div>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" placeholder="{{ translate('Discount') }}"
                                name="discount" aria-label="discount" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="percent">Percent</option>
                                    <option value="tk">TK</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary" name="submit"
                                value="submit">{{ translate('Update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        @if ($order->is_b2b_order && count($sale_payments) > 0)
            <div class="row">
                <div class="col">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale_payments as $sp)
                                <tr>
                                    <td>{{ single_price($sp->amount) }}</td>
                                    <td>{{ $sp->payment_date }}</td>
                                    <td>{{ $sp->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdn.socket.io/4.5.3/socket.io.min.js"></script>

    <script>
        const socket = io("{{ env('APP_URL') }}", {path: "/api/nextjs/socket"});
    </script>

    <script type="text/javascript">

        $('#assign_deliver_boy').on('change', function() {

            var order_id = {{ $order->id }};

            var delivery_boy = $('#assign_deliver_boy').val();

            $.post('{{ route('orders.delivery-boy-assign') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                delivery_boy: delivery_boy
            }, function(resdata) {

                console.log(resdata);

                var message = "An Order has been Assigned to you";
                var title = "Order Assigned";
                var data = {
                    'registration_ids': [resdata.token],
                    "notification": {
                        "title": `${title}`,
                        "body": `${message}`,
                        "mutable_content": true,
                        "sound": "Tri-tone"
                    },
                    "data": {
                        "url": "<url of media image>",
                        "dl": "<deeplink action on tap of notification>"
                    }
                };

                console.log(data);

                $.ajax({
                    type: "POST",
                    url: "https://fcm.googleapis.com/fcm/send",
                    dataType: "json",
                    data: JSON.stringify(data),
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "key=AAAAGMOtpGU:APA91bEw_evJYhAfVFDdpTX5VrvHE-omPtNgbqc3Ma4fIjWUWyTTBnxMJ6nQqH9JD1V7NEjNT4EwrdCGctilL_hl5qJTrpy21dkRvV_Ty5g_BXqnpFSCnFy_tsrQ_3CbQJWenFy4-hr3"
                    },
                    success: function(response) {
                        console.log(response);
                        AIZ.plugins.notify('success', '{{ translate('Delivery boy has been assigned') }}');
                    },
                    error: function(err){
                        console.log(err);
                    }
                });

            });
        });

        $('#update_delivery_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();

            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {

                if(status == "processing"){
                    socket.emit("orderprocess", @json($socket_order));
                }

                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
            });
        });

        $('#update_payment_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            });
        });

        $('#update_tracking_code').on('change', function() {
            var order_id = {{ $order->id }};
            var tracking_code = $('#update_tracking_code');
        });

        $('#manage_by').on('change', function(e) {

            var order_id = {{ $order->id }};
            var manage_by = e.target.value;

            $.post('{{ route('orders.manageby') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                manage_by: manage_by
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Updated Successfully') }}');
            });
        });
    </script>

    <script>

        $(document).ready(function(){

            $("#refund_details").on("click", function(){

                $.ajax({
                    url: '{{ route('bkash.refund.details') }}',
                    type: 'POST',
                    data: JSON.stringify({order_id: "{{ $order->id }}", _token: "{{ csrf_token() }}"}),
                    contentType: 'application/json',
                        success: function(data) {
                            $("#bkashrefundcontentloader").hide();
                            $("#bkashrefundcontent").show();
                        },
                        error: function(err) {
                            console.log(err);
                        }
                });

            });


            $('.js-data-example-ajax').select2({
                placeholder: "Select Product",
                ajax: {
                    url: '/api/search-products',
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    dataType: 'json',
                    processResults: function (data) {
                        var res = data.data.map(function (item) {
                            return {id: item.id, text: item.name};
                        });
                        return {
                            results: res
                        };
                    }
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                }
            });

            // $("#product_id").siblings(".dropdown-menu").find("input").on("keyup", function(e){

            //     var value = e.target.value;

            //     $.ajax({
            //         url: '/api/search/'+value,
            //         type: 'GET',
            //         contentType: 'application/json',
            //             success: function(data) {
            //                 let html = "";
            //                 let html2 = "";
            //                 data.data.forEach((d, i) => {
            //                     html2 += "<option value='"+d.id+"'>"+d.name+"</option>";
            //                     html += `<li><a role="option" class="dropdown-item" id="bs-select-1-${i+1}" tabindex="0" aria-setsize="48" aria-posinset="${i}"><span class="text">
            //                         ${d.name}
            //                     </span></a></li>`;
            //                 });

            //                 console.log(html);
            //                 $("#product_id").html(html2);
            //                 $("#product_id").siblings(".dropdown-menu").find(".dropdown-menu").html(html);
            //                 $("#product_id").selectpicker({
            //                     size: 5,
            //                     noneSelectedText: AIZ.local.nothing_selected,
            //                     virtualScroll: false
            //                 });
            //             },
            //             error: function(err) {
            //                 console.log(err);
            //             }
            //     });

            // });





            // $("#bkashrefundform").on("submit", function(e){
            //     e.preventDefault();

            //     // const refundtoken = $("#refund_token").val();

            //     const paymentDetails = @json(json_decode($order->payment_details));
            //     const xappkey = "{{ env('BKASH_CHECKOUT_APP_KEY') }}";
            //     const refund_amount = $("#refund_amount").val();
            //     const refund_reason = $("#refund_reason").val();
            //     const sku = $("#sku").val();
            //     const refund_token = $("#refund_token").val();

            //     const requestData = {
            //         "paymentID": paymentDetails.paymentID,
            //         "amount": refund_amount,
            //         "trxID": paymentDetails.trxID,
            //         "sku": sku,
            //         "reason": refund_reason
            //     };


            //     $.ajax({
            //         url: "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/refund",
            //         headers: {
            //             'Authorization': refund_token,
            //             'X-APP-Key':"{{ env('BKASH_CHECKOUT_APP_KEY') }}",
            //             'Content-Type':'application/json'
            //         },
            //         type: 'POST',
            //         data: JSON.stringify(requestData),
            //         success: function(data) {
            //             console.log(data);
            //         },
            //         error: function(err) {
            //             console.log(err);
            //         }
            //     });

            // });

        });
    </script>

@endsection
