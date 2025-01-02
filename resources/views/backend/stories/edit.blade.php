@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Story')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Story Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('stories.update', $story->id) }}" method="POST">
            	@csrf
                @method('PUT')
                <input type="hidden" name="story_group_id" value="{{ request('storygroup') }}">
                <div class="form-group row">

                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}} <small>(300x300)</small></label>
                    <div class="col-md-8">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="thumbnail_img" value="{{ $story->upload_id }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        <small class="text-muted">{{translate('This image is visible in all product box. Use 300x300 sizes image. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.')}}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="type">{{ translate('Type') }}</label>
                    <div class="col-sm-9">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="type"
                            name="type" required>
                            <option value="">{{ translate('Type') }}</option>
                            <option value="image" @if($story->type  == 'image')selected @endif >Image</option>
                            <option value="video" @if($story->type  == 'video')selected @endif>Video</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="duration">{{translate('Duration')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Duration')}}" id="duration" name="duration" class="form-control" value="{{ $story->duration}}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="navigation_url">{{ translate('Navigation URL') }}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{ translate('Navigation URL') }}" id="navigation_url" name="navigation_url"
                            class="form-control"  value="{{ $story->navigation_url }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="show_on_front">{{ translate('Status') }}</label>
                    <div class="col-sm-9">
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input value="1" name="show_on_front" type="checkbox" @if ($story->show_on_front) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="is_active">{{ translate('Status') }}</label>
                    <div class="col-sm-9">
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input value="1" name="is_active" type="checkbox" @if ($story->is_active) checked @endif>
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
