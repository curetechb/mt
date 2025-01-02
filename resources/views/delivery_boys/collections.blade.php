@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All Collection List')}}</h1>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header d-block d-lg-flex">
        <h5 class="mb-0 h6">{{translate('Collection List')}}</h5>
        <div class="">
            <form class="" id="sort_delivery_boys" action="" method="GET">

                <div class="row">
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
                    <div class="col">
                        <div class="form-group mb-0 d-flex">
                            <button type="submit" class="btn btn-primary" name="filter" value="filter">{{ translate('Filter') }}</button>
                            {{-- <button type="submit" class="btn btn-info ml-1" name="export" value="export">{{ translate('Download') }}</button> --}}
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Delivery Boy')}}</th>
                    <th class="text-center">{{translate('Collected Amount')}}</th>
                    <th class="text-right">{{translate('Collected At')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($collections as $key => $collection)

                    <tr>
                        <td>{{ ($key+1) + ($collections->currentPage() - 1) * $collections->perPage() }}</td>
                        <td>
                            {{ $collection->user->name }}
                        </td>
                        <td class="text-center">
                            {{ single_price($collection->grand_total) }}
                        </td>
                        <td class="text-right">
                            {{ date("d-M-Y h:iA", strtotime($collection->delivery_time)) }}
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $collections->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection
