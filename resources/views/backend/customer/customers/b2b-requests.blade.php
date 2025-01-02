@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row">
        <div class="col-md-6 align-items-center">
            <h1 class="h3">{{translate('All B2B Requests')}}</h1>
        </div>
    </div>
</div>


<div class="card">
    <form class="" id="sort_customers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('Customers')}} ({{ $users->total() }})</h5>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <!--<th data-breakpoints="lg">#</th>-->
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th>{{translate('SL No')}}</th>
                        <th>{{translate('Name')}}</th>
                        <th>{{translate('Phone')}}</th>
                        <th>{{translate('Referral Code')}}</th>
                        <th>{{translate('Points')}}</th>
                        <th class="text-center">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    @if ($user != null)
                    <tr>
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{$user->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->phone}}</td>
                        <td>{{$user->referral_code}}</td>
                        <td>{{$user->points}}</td>
                        <td class="text-right">


                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('customers.edit', $user->id)}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                           </a>

                            {{-- <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('customers.destroy', $user->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a> --}}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $users->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection
