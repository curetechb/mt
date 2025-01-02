@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Vendor')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('vendors.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Vendor')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Vendors')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{ translate('Company Name') }}</th>
                    <th>{{ translate('Contact Name') }}</th>
                    <th>{{ translate('Contact Number') }}</th>
                    <th>{{ translate('Address') }}</th>
                    <th>{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendors as $key => $vendor)
                    <tr>
                        <td>{{ ($key+1) + ($vendors->currentPage() - 1)*$vendors->perPage() }}</td>
                        <td>{{$vendor->company_name}}</td>
                        <td>{{$vendor->contact_name}}</td>
                        <td>{{$vendor->contact_number}}</td>
                        <td>
                            {{$vendor->address}}
                        </td>
                        <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('vendors.edit', $vendor->id)}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('vendors.destroy', $vendor->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $vendors->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
