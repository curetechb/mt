@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('Story Groups')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('story-group.create')}}" class="btn btn-circle btn-info">
                <span>{{translate('Add Story Group')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="row">
    @foreach ($storygroups as $storygroup)
        <div class="col-md-2 mb-3">
            <div class="card">
                <div class="card-body p-1">
                    <div>
                        <img src="{{ uploaded_asset($storygroup->stories()->where('show_on_front', true)->first()->upload_id ?? "") }}" alt="" class="img-fluid">
                    </div>
                    <div class="p-2">
                        <p class="mb-2 fw-600">{{ $storygroup->name }}</p>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route("stories.index", ['storygroup' => $storygroup->id]) }}" class="btn btn-info btn-xs">Stories</a>
                            <div>
                                <a href="{{ route("story-group.edit", $storygroup->id) }}" class="btn btn-primary btn-xs"><i class="las la-edit"></i></a>
                                <a href="{{ route("story-group.destroy", $storygroup->id) }}" class="btn btn-danger btn-xs"><i class="las la-trash"></i></a>
                            </div>
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
