@extends('frontend.layouts.app')
@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                @yield('panel_content')
            </div>
        </div>
    </div>
</section>
@endsection
