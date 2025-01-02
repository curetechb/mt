@extends('backend.layouts.app')

@section('content')

<div class="text-left">
  <h5>{{ translate('Add New Customer')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0 h6">{{ translate('Customer Information')}}</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('customers.update', $user->id ) }}" method="POST">
        @csrf
        @method("PUT")
        <div class="form-group row">
           <label class="col-sm-3 col-from-label" for="name">{{ translate('Name')}}</label>
           <div class="col-sm-9">
            <input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Name') }}" value="{{ $user->name }}">
           </div>
        </div>

        <!-- <div class="form-group row">
           <label class="col-sm-3 col-from-label" for="email">{{ translate('Email')}}</label>
           <div class="col-sm-9">
            <input type="email" class="form-control" id="email" name="email" placeholder="{{ translate('Email') }}" value="{{ $user->email }}">
           </div>
        </div> -->

        <div class="form-group row">
           <label class="col-sm-3 col-from-label" for="phone">{{ translate('Phone')}}</label>
           <div class="col-sm-9">
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="{{ translate('Phone') }}" value="{{ $user->phone }}">
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
            <div class="col-sm-3 col-from-label">
                <label>{{ translate('Address')}}</label>
            </div>
            <div class="col-sm-9">
                <textarea class="form-control mb-3" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required>{{ $address->address ?? "" }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 col-from-label">
                <label>{{ translate('Area')}}</label>
            </div>
            <div class="col-sm-9">
                <div class="mb-3">
                    <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your Area')}}" name="area" required>
                        <option value="">{{ translate('Select your Area') }}</option>
                        @foreach (\App\Models\State::where('status', 1)->get() as $key => $state)
                            <option value="{{ $state->id }}" @if($state->id == $address?->state_id) selected @endif>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 col-from-label">
                <label>{{ translate('Floor No')}}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control mb-3" placeholder="{{ translate('Floor No')}}" name="floor_no" value="{{ $address->floor_no ?? "" }}">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 col-from-label">
                <label>{{ translate('Apartment')}}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control mb-3" placeholder="{{ translate('Apartment')}}" name="apartment" value="{{ $address->apartment ?? "" }}">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 col-from-label">
                <label>{{ translate('Name')}}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control mb-3" placeholder="{{ translate('Name')}}" name="name" required value="{{ $address->name ?? "" }}">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-from-label">
                <label>{{ translate('Alternative Phone')}}</label>
            </div>
            <div class="col-sm-9">
                <input type="text" class="form-control mb-3" placeholder="{{ translate('+880')}}" name="alternative_phone" value="{{ $address->phone ?? "" }}">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-from-label">{{translate('Is B2B Customer')}}</label>
            <div class="col-sm-3">
                <label class="aiz-switch aiz-switch-success mb-0" style="margin-top:5px;">
                    <input type="checkbox" name="is_b2b_user" @if($user->is_b2b_user) checked @endif>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>


        <!-- <div class="form-group row">
           <label class="col-sm-3 col-from-label" for="password">{{ translate('Password')}}</label>
           <div class="col-sm-9">
            <input type="password" class="form-control" id="password" name="password" placeholder="{{ translate('Password') }}">
           </div>
        </div> -->

        <div class="form-group mb-0 text-right">
            <input type="hidden" name="redirect_to" value="{{ request("redirect_to", "") }}">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>

      </form>
    </div>
  </div>
</div>

@endsection
