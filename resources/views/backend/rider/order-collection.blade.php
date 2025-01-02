@extends('backend.layouts.app')

@section('content')
<div>
    <h5 class="h6">{{ translate('All Orders') }} ({{ $histories->total() }})</h5>
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
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Rider') }}</th>
                        <th data-breakpoints="md">{{ translate('Collection Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Commision Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Collection Time') }}</th>
                        {{-- <th class="text-right" width="15%">{{translate('options')}}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $key => $history)
                    <tr>

                        <td>
                           #{{ $history->order->code ?? "" }}
                        </td>
                        <td>
                            {{ $history->rider->user->name ?? "" }}
                        </td>

                        <td>
                            {{ single_price($history->collection) }}
                        </td>

                        <td>
                            {{ single_price($history->earning) }}
                        </td>

                        <td>
                            {{ $history->created_at->format("d-m-Y h:iA") }}

                        </td>

                        {{-- <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('all_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                        </td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $histories->appends(request()->input())->links() }}
            </div>

        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
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
//                url: "{{route('bulk-order-status')}}",
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
                url: "{{route('bulk-order-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection
