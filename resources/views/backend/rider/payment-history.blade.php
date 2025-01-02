@extends('backend.layouts.app')

@section('content')
<div>
    <h5 class="h6">{{ translate('All Payments') }} ({{ $payments->total() }})</h5>
</div>
<div class="card">

    <form class="" action="" id="sort_orders" method="GET">

        <div class="card-header row gutters-5">

            <div class="col">
                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                    id="rider" name="rider">
                    <option value="">{{ __("Select Rider") }}</option>
                    @foreach ($riders as $rider)
                        @if ($rider->user)
                            <option @if(request("rider") == $rider->id) selected @endif value="{{ $rider->id }}">{{ $rider->user->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="col">
                <div class="form-group mb-0">
                    <input type="date" class="form-control" name="start_date"  id="start_date" placeholder="{{ translate('Start Date') }}" value="{{ $start_date }}" date-format="DD-MM-Y">
                    {{-- <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off"> --}}
                </div>
            </div>
            <div class="col">
                <div class="form-group mb-0">
                    <input type="date" class="form-control" name="end_date" id="end_date" placeholder="{{ translate('End Date') }}" value="{{ $end_date }}" date-format="DD-MM-Y">
                </div>
            </div>

            {{-- <div class="col">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Query') }}">
                </div>
            </div> --}}

            <div class="col">
                <div class="form-group mb-0 d-flex">
                    <button type="submit" class="btn btn-primary" name="filter" value="filter">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="md">{{ translate('Rider') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Status') }}</th>
                        <th class="text-right" width="15%">{{translate('options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $key => $payment)
                    <tr>


                        <td>
                            {{ $payment->rider->user->name ?? "" }}
                        </td>

                        <td>
                            {{ single_price($payment->amount) ?? "" }}
                        </td>

                        <td>
                            @if ($payment->status == "pending")
                                <span class="badge badge-inline badge-info">Pending</span>
                            @elseif($payment->status == "processing")
                                <span class="badge badge-inline badge-warning">Processing</span>
                            @elseif($payment->status == "paid")
                                <span class="badge badge-inline badge-success">Paid</span>
                            @elseif($payment->status == "cancelled")
                                <span class="badge badge-inline badge-info">Cancelled</span>
                            @endif
                        </td>


                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('rider.payment.show', $payment->id)}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $payments->appends(request()->input())->links() }}
            </div>

        </div>
    </form>
</div>

@endsection
