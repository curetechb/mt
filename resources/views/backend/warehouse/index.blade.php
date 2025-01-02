@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Warehouse')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('warehouse.create')}}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Warehouse')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Warehouses')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{ translate('Name') }}</th>
                    <th>{{ translate('Address') }}</th>
                    <th>{{ translate('Latitude') }}</th>
                    <th>{{ translate('Longitude') }}</th>
                    <th>{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($warehouses as $key => $warehouse)
                    <tr>
                        <td>{{ ($key+1) + ($warehouses->currentPage() - 1)*$warehouses->perPage() }}</td>
                        <td>{{$warehouse->name}}</td>
                        <td>{{$warehouse->address}}</td>
                        <td>{{$warehouse->latitude}}</td>
                        <td>
                            {{$warehouse->longitude}}
                        </td>
                        <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('warehouse.edit', $warehouse->id)}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('warehouse.destroy', $warehouse->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{$warehouses->appends(request()->input())->links()}}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
