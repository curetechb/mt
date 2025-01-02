@extends('backend.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="h6">{{ translate('All Orders') }} ({{ $orders->total() }})</h5>
        <a href="{{ route('newsale') }}" class="btn btn-primary">New Sale</a>
    </div>
    <div class="row">

            <div class="card col-12">

                <form class="" action="" id="sort_orders" method="GET">

                    <div class="card-header row gutters-5">

                        <div class="col-12 col-md-2">
                            <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                                <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                                    {{ translate('Pending') }}</option>
                                <option value="processing" @if ($delivery_status == 'processing') selected @endif>
                                    {{ translate('Processing') }}</option>
                                <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>
                                    {{ translate('On The Way') }}</option>
                                <option value="next_day" @if ($delivery_status == 'next_day') selected @endif>
                                    {{ translate('Next Day') }}</option>
                                <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                    {{ translate('Delivered') }}</option>
                                <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>
                                    {{ translate('Canceled') }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 mt-3">
                            <select class="form-control aiz-selectpicker" name="customer" id="customer">
                                <option value="">{{ translate('Select Customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name . " ($customer->phone) " }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-2 mt-3">
                            <div class="form-group mb-0">
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    placeholder="{{ translate('Start Date') }}" value="{{ $start_date }}"
                                    date-format="DD-MM-Y">
                                {{-- <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off"> --}}
                            </div>
                        </div>
                        <div class="col-12 col-md-2 mt-3">
                            <div class="form-group mb-0">
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    placeholder="{{ translate('End Date') }}" value="{{ $end_date }}"
                                    date-format="DD-MM-Y">
                            </div>
                        </div>
                        <div class="col-12 col-md-2 mt-3">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control" id="search"
                                    name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                                    placeholder="{{ translate('Query') }}">
                            </div>
                        </div>

                        <div class="col-12 col-md-2 mt-3">
                            <div class="form-group mb-0 d-flex">
                                <button type="submit" class="btn btn-primary" name="filter"
                                    value="filter">{{ translate('Filter') }}</button>
                                @if (Auth::user()->user_type == 'admin' || in_array(Auth::user()->staff->role->id, [1, 2, 5]))
                                    <button type="submit" class="btn btn-info ml-1" name="export" value="export1"
                                        data-toggle="tooltip" data-placement="top" title="Admin Format">
                                        <i class="las la-download"></i>
                                    </button>
                                    <button type="submit" class="btn btn-info ml-1" name="export" value="export2"
                                        data-toggle="tooltip" data-placement="top" title="Acocunts Format">
                                        <i class="las la-download"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="card-body">
                        <table class="table aiz-table mb-0">
                            <thead>
                                <tr>
                                    <!--<th>#</th>-->
                                    {{-- <th>
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-all">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            </th> --}}
                                    <th>{{ translate('Order Code') }}</th>
                                    <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                                    <th data-breakpoints="md">{{ translate('Customer') }}</th>
                                    <th data-breakpoints="md">{{ translate('Area') }}</th>
                                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                                    <th data-breakpoints="md">{{ translate('Paid') }}</th>
                                    <th data-breakpoints="md">{{ translate('Due') }}</th>
                                    <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                                    <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                                    <th data-breakpoints="md">{{ translate('Order Time') }}</th>
                                    <th class="text-right" width="15%">{{ translate('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $key => $order)
                                    <tr>
                                        <!--
                                <td>
                                    {{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}
                                </td>-->
                                        {{-- <td>
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-one" name="id[]" value="{{$order->id}}">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            </td> --}}
                                        <td>
                                            {{ $order->invoice_number ?: $order->code }}
                                        </td>
                                        <td>
                                            {{ count($order->orderDetails) }}
                                        </td>
                                        <td>
                                            {{ $order->user->phone ?? '' }}
                                        </td>
                                        <td>
                                            {{ json_decode($order->shipping_address)->state ?? '' }}
                                        </td>
                                        <td>
                                            {{ single_price($order->grand_total - $order->coupon_discount) }}
                                        </td>
                                        <td>
                                            {{ single_price($order->paid_amount) }}
                                        </td>
                                        <td>
                                            {{ single_price($order->due_amount) }}
                                        </td>
                                        <td>
                                            @php
                                                $status = $order->delivery_status;
                                                if ($order->delivery_status == 'cancelled') {
                                                    $status = '<span class="badge badge-inline badge-danger" data-toggle="tooltip" data-placement="top" title="' . $order->reason . '">' . translate('Cancel') . '</span>';
                                                }
                                            @endphp
                                            {!! $status !!}
                                        </td>
                                        <td>
                                            @if ($order->payment_status == 'paid')
                                                <span
                                                    class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                            @elseif($order->payment_status == 'refunded')
                                                <span
                                                    class="badge badge-inline badge-warning">{{ translate('Refunded') }}</span>
                                            @elseif($order->payment_status == 'partially_paid')
                                                <span
                                                    class="badge badge-inline badge-warning">{{ translate('Partially Paid') }}</span>
                                            @else
                                                <span
                                                    class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- {{ date('d-m-Y H:i A', $order->date) }} --}}
                                            {{ $order->created_at->format('d-m-Y H:i A') }}
                                        </td>

                                        <td class="text-right">
                                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                href="{{ route('all_orders.show', encrypt($order->id)) }}"
                                                title="{{ translate('View') }}">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                                href="{{ route('invoice.download', $order->id) }}"
                                                title="{{ translate('Download Invoice') }}">
                                                <i class="las la-download"></i>
                                            </a>
                                            @if (Auth::user()->user_type == 'admin' && request('name') == 'salim')
                                                <a href="#"
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                    data-href="{{ route('orders.destroy', $order->id) }}"
                                                    title="{{ translate('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="aiz-pagination">
                            {{ $orders->appends(request()->input())->links() }}
                        </div>

                    </div>
                </form>

        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        //        function change_status() {
        //            var data = new FormData($('#order_form')[0]);
        //            $.ajax({
        //                headers: {
        //                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                },
        //                url: "{{ route('bulk-order-status') }}",
        //                type: 'POST',
        //                data: data,
        //                cache: false,
        //                contentType: false,
        //                processData: false,
        //                success: function (response) {
        //                    if(response == 1) {
        //                        location.reload();
        //                    }
        //                }
        //            });
        //        }

        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-order-delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection
