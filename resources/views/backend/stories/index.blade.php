@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('Story')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('stories.create',['storygroup' => request('storygroup')])}}" class="btn btn-circle btn-info">
                <span>{{translate('Add Story')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="row">
    @foreach ($stories as $story)
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-1">
                    <div>
                        @if ($story->type == 'video')
                            <video controls style="width: 100%; height: 100%;">
                                <source src="{{ uploaded_asset($story->upload_id) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ uploaded_asset($story->upload_id) }}" alt="" class="img-fluid">
                        @endif
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <div>
                                {{$story->type}}
                            </div>
                            <div>
                                {{$story->duration}} sec
                            </div>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route("stories.edit", [ 'story' => $story->id, 'storygroup' => request('storygroup')]) }}">Edit</a>
                            <div class="mx-1"></div>
                            <a href="{{ route("stories.destroy", $story->id) }}">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
