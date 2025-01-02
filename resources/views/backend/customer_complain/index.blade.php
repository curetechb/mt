@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('Customer Complain')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{route('complain.export')}}" class="btn btn-circle btn-primary">
                <span>{{translate('Export')}}</span>
            </a>
            <a href="{{route('complain.create')}}" class="btn btn-circle btn-info">
                <span>{{translate('Add Customer Complain')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Complain')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>{{ translate('ID') }}</th>
                    <th data-breakpoints="lg">{{ translate('Order Number') }}</th>
                    <th data-breakpoints="lg">{{ translate('Description') }}</th>
                    <th data-breakpoints="lg">{{ translate('Status') }}</th>
                    <th data-breakpoints="lg">{{ translate('Time') }}</th>
                    <th width="14%">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complains as $key => $complain)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{$complain->order->code ?? "00000"}}</td>
                    <td>{{$complain->description}}</td>
                    <td>{{$complain->status}}</td>
                    <td>{{ $complain->created_at->format("d-M-Y h:iA") }}</td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('complain.edit', $complain->id)}}" title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('complain.destroy', $complain->id)}}" title="{{ translate('Delete') }}">
                            <i class="las la-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection

@section('modal')
 @include('modals.delete_modal')
@endsection
