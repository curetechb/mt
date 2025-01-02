@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Story Group')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Story Group Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('story-group.update', $storyGroup->id) }}" method="POST">
            	@csrf
                @method("PUT")

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ $storyGroup->name }}" required>
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}} <small>(290x300)</small></label>
                    <div class="col-md-8">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="thumbnail_img" value="{{ $storyGroup->upload_id }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div> --}}

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="navigation_url">{{ translate('Status') }}</label>
                    <div class="col-sm-9">
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input value="1" name="is_active" type="checkbox" @if ($storyGroup->is_active) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
