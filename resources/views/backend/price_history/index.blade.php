@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1>{{ translate('All Price History') }}</h1>
            </div>

            <!-- <div class="col text-right">
                <a href="{{ route('cancel_list.create') }}" class="btn btn-circle btn-info">
                    <span>{{ translate('Add price History') }}</span>
                </a>
            </div> -->
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{-- <h5 class="m-0 h6">{{translate('Price History List')}}</h5> --}}

            <form action="{{ route('price-histories.index')}}" method="GET">
                <div class="row">
                    <div class="col">
                        <div class="form-group mb-0">
                            <input type="date" class="form-control" name="start_date" id="start_date"
                                placeholder="{{ translate('Start Date') }}" value="{{ $start_date }}"
                                date-format="DD-MM-Y">
                            {{-- <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off"> --}}
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group mb-0">
                            <input type="date" class="form-control" name="end_date" id="end_date"
                                placeholder="{{ translate('End Date') }}" value="{{ $end_date }}" date-format="DD-MM-Y">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control" id="search"
                                name="search"@isset($search) value="{{ $search }}" @endisset
                                placeholder="{{ translate('Query') }}">
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary" name="filter"
                            value="filter">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    </div>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('User Name') }}</th>
                    <th>{{ translate('Product Name') }}</th>
                    <th>{{ translate('Price') }}</th>
                    <th>{{ translate('Created') }}</th>
                    <!-- <th width="10%">{{ translate('Options') }}</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach ($histories as $key => $history)
                    <tr>
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $history->user->name ?? "" }}</td>
                        <td>{{ $history->product->name ?? "" }}</td>
                        <td>{{ $history->price }}</td>
                        <td>{{ $history->created_at->format("d-M-Y") }}</td>
                        <!-- <td class="text-right">
              <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="" title="{{ translate('Edit') }}">
                <i class="las la-edit"></i>
              </a>
              <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="" title="{{ translate('Delete') }}">
                <i class="las la-trash"></i>
              </a>
            </td> -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
