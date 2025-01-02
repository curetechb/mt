@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col text-right">
            <a href="{{ route('stockin.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add Stock In')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Stock In History')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{ translate('Product') }}</th>
                    <th>{{ translate('Vendor') }}</th>
                    <th>{{ translate('Stock In') }}</th>
                    <th>{{ translate('Note') }}</th>
                    <th width="10%">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockins as $key => $stockin)
                    <tr>
                        <td>{{ ($key+1) + ($stockins->currentPage() - 1)*$stockins->perPage() }}</td>
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($stockin->product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $stockin->product->getTranslation('name') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $stockin->vendor->company_name ?? "" }}
                        </td>
                        <td>
                            {{ $stockin->quantity }}
                        </td>
                        <td>
                            {{ $stockin->note }}
                        </td>
                        <td class="text-right">
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('stockin.destroy', $stockin->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $stockins->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
