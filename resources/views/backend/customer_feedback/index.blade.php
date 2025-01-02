@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('Customer Feedback')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{route('feedback.export')}}" class="btn btn-circle btn-primary">
                <span>{{ translate('Export')}}</span>
            </a>
            <a href="{{route('feedback.create')}}" class="btn btn-circle btn-info">
                <span>{{translate('Add Customer Feedback')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Feedback')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>

                    <th>{{ translate('ID') }}</th>
                    <th data-breakpoints="lg">{{ translate('Product') }}</th>
                    <th data-breakpoints="lg">{{ translate('Price') }}</th>
                    <th data-breakpoints="lg">{{ translate('Delivery') }}</th>
                    <th data-breakpoints="lg">{{ translate('Note') }}</th>
                    <th data-breakpoints="lg">{{ translate('Time') }}</th>
                    <th width="12%">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($feedbacks as $key => $feedback)
                <tr>

                    <td>{{ ($key+1) }}</td>
                    <td>{{$feedback->product}}</td>
                    <td>{{$feedback->price}}</td>
                    <td>{{$feedback->delivery}}</td>
                    <td>{{$feedback->note}}</td>
                    <td>{{ $feedback->created_at->format("d-M-Y h:iA") }}</td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('feedback.edit', $feedback->id)}}" title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('feedback.destroy', $feedback->id)}}" title="{{ translate('Delete') }}">
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
