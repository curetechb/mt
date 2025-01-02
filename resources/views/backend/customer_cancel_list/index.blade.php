@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
  <div class="row align-items-center">
    <div class="col-auto">
      <h1>{{translate('All Customer Cancel List')}}</h1>
    </div>


    <div class="col text-right">

        <a href="{{route('cancel_list.export')}}" class="btn btn-circle btn-primary">
            <span>{{translate('Bulk Export')}}</span>
          </a>
      <a href="{{ route('cancel_list.create') }}" class="btn btn-circle btn-info">
        <span>{{translate('Add New Customer Cancel List')}}</span>
      </a>
    </div>


  </div>
</div>

<div class="card">
  <div class="card-header  d-block d-lg-flex">
    <h5 class="m-0 h6">{{translate('Customer Cancel List')}}</h5>
  </div>
</div>

<div class="card-body">
  <table class="table aiz-table mb-0">
    <thead>
      <tr>
        <th>#</th>
        <th>{{translate('Date')}}</th>
        <th>{{translate('Notes')}}</th>
        <th>{{translate('Total Order')}}</th>
        <th>{{translate('Cancel')}}</th>
        <th>{{translate('Delivery')}}</th>
        <th>{{translate('Next Day')}}</th>
        <th>{{translate('Processing')}}</th>
        <th width="10%">{{translate('Options')}}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cancels as $key=>$cancel)
      <tr>
      <tr>
        <td>{{ ($key+1) }}</td>
        <td>{{$cancel->date}}</td>
        <td>{{$cancel->notes}}</td>
        <td>{{$cancel->total_order}}</td>
        <td>{{$cancel->cancel}}</td>
        <td>{{$cancel->delivery}}</td>
        <td>{{$cancel->nextday}}</td>
        <td>{{$cancel->processing}}</td>
        <td class="text-right">
          <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('cancel_list.edit', $cancel->id)}}" title="{{ translate('Edit') }}">
            <i class="las la-edit"></i>
          </a>
          <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('cancel_list.destroy', $cancel->id)}}" title="{{ translate('Delete') }}">
            <i class="las la-trash"></i>
          </a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

@endsection

@section('modal')
@include('modals.delete_modal')
@endsection
