@extends('backend.layouts.app')

@push('head_tags')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
    </style>
@endpush


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6"> {{ translate('Send Push Notification') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="title" class="">{{ translate('Title') }}</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Enter Title">
                        </div>
                        <div class="form-group row">
                            <label for="message" class="">{{ translate('Message') }}</label>
                            <input type="text" id="message" name="message" class="form-control" placeholder="Enter Message">
                        </div>
                        
                        <div class="form-group row">
                            <label for="product_list">{{ translate('Product List')}}</label>
                            <select class="js-data-example-ajax" name="product_slug" id="product_slug" required style="width: 100%"></select>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary" id="save">{{ translate('Save') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script>

        $(document).ready(function() {

            $('#save').click(function() {

                var product_slug = $("#product_slug").val();

                var message = $("#message").val();
                var title = $("#title").val();
                var data = {
                            "title": `${title}`,
                            "description": `${message}`,
                            "product_slug": product_slug
                            };

                $.ajax({
                    type: "POST",
                    url: "{{ route('send.push.notification') }}",
                    dataType: "json",
                    data: JSON.stringify(data),
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        alert("success");
                    },
                    error: function(err){
                        console.log(err);
                    }
                });

            });


            $('.js-data-example-ajax').select2({
                placeholder: "Select Product",
                ajax: {
                    url: '/api/search-products',
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    dataType: 'json',
                    processResults: function (data) {
                        var res = data.data.map(function (item) {
                            return {id: item.slug, text: item.name};
                        });
                        return {
                            results: res
                        };
                    }
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                }
            });

        });
        

    </script>
@endsection
