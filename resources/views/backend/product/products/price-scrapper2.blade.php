@extends('backend.layouts.app')

@section('content')

@php
    ini_set('max_execution_time', 3000);
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All products')}} ({{ $products->total() }})</h1>
        </div>
        <div class="col text-right">


            {{-- <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Fetch Price')}}</span>
            </a> --}}

        </div>
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="{{ route("price.scrapper2") }}" method="POST">
        @csrf

        <div class="card-header row gutters-5">
            <div class="d-flex align-items-center justify-content-between w-100">
                <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
                <div class="d-flex align-items-center">
                    <button type="button" id="start_automatic" class="btn btn-info">
                        Start Automatic
                    </button>
                    <div class="mx-2"></div>
                    <button type="button" id="stop_automatic" class="btn btn-danger">
                        Stop Automatic
                    </button>
                    <div class="mx-2"></div>
                    <div class="form-group mb-0">
                        <select name="per_page" id="per_page" class="form-control" onchange="window.location.href = `{{route('price.scrapper2')}}?per_page=${event.target.value}`">
                            <option value="">Per Page</option>
                            <option @if(request('per_page') == '50' ) selected @endif value="50">50</option>
                            <option @if(request('per_page') == '100' ) selected @endif value="100">100</option>
                            <option @if(request('per_page') == '200' ) selected @endif value="200">200</option>
                            <option @if(request('per_page') == '300' ) selected @endif value="300">300</option>
                            <option @if(request('per_page') == '400' ) selected @endif value="400">400</option>
                            <option @if(request('per_page') == '500' ) selected @endif value="500">500</option>
                            <option @if(request('per_page') == '1000' ) selected @endif value="1000">1000</option>
                        </select>
                    </div>
                    <div class="mx-2"></div>
                    <button type="submit" class="btn btn-circle btn-info">
                        <span>{{ translate('Update Price') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>{{translate('Name')}}</th>
                        <th>{{translate('MT Price')}}</th>
                        <th>{{translate('CD Link')}}</th>
                        <th>{{translate('CD Price')}}</th>
                        <th>{{translate('Action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $key => $product)
                    <tr>

                        <td>
                            {{ $loop->index + 1 }}
                        </td>

                        <td>
                            {{ $product->name }}
                        </td>

                        <td>
                            ৳{{ $product->unit_price }}
                        </td>
                        <td>
                            <input type="text" class="form-control cdlink" value="{{ $product->clink }}">
                        </td>
                        <td>
                            <input type="text" class="form-control cdprice" name="prices[{{ $product->id }}]">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger getCDprice">Get Price</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection


@section('script')
    <script type="text/javascript">

    $(document).ready(function(){

        $(document).on("click", ".getCDprice",function(){

            $(this).prop("disabled",true);

            // var price = $(this).parent().prev().find(".cdprice").val();
            var clink = $(this).parent().prev().prev().find(".cdlink").val();

            if(clink == ""){
                $(this).prop("disabled",false);
                return alert("CD Link is Empty. Fill it First.");
            }

            var thisobj = this;
            var priceinput = $(this).parent().prev().find(".cdprice");

            $.ajax({
                type:"GET",
                url: "{{ route('getcdprice') }}?clink="+clink,
                success: function(data){

                    var txt  = data;
                    var numb = txt.match(/\d/g);
                    numb = numb.join("");

                    $(thisobj).prop("disabled",false);
                    priceinput.val(parseInt(numb) - 2);

                    // const price = parseInt(data.replace("৳", ""));
                    // $("#unit_price").val(parseInt(numb) - 2);
                    // if(typeof price == "number"){
                    //     $("#unit_price").val(price);
                    // }else{
                    //     alert("Something Wen't Wrong");
                    // }

                },
                error: function(err){
                    alert("Something Wen't Wrong");
                    $(thisobj).prop("disabled",false);
                }
            });

        });


        var interval = null;

        $(document).on("click", "#start_automatic", function(){


            $(this).prop("disabled", true);

            let i = 1;

            interval = setInterval(function () {


                const tr = document.getElementsByTagName("tr");
                if(i >= tr.length){
                    clearInterval(interval);
                    alert("Finished");
                    return;
                }

                console.log(i);

                const clink = tr[i].getElementsByClassName("cdlink")[0].value;
                const cdprice = tr[i].getElementsByClassName("cdprice")[0];
                const cdbutton = tr[i].getElementsByClassName("getCDprice")[0];

                if(!clink) i++;

                cdbutton.disabled = true;

                $.ajax({
                    type:"GET",
                    url: "{{ route('getcdprice') }}?clink="+clink,
                    success: function(data){

                        var txt  = data;
                        var numb = txt.match(/\d/g);

                        console.log(numb);
                        if(Array.isArray(numb)){
                            numb = numb.join("");
                            cdprice.value = parseInt(numb) - 2;
                        }else{
                            cdprice.value = "";
                        }

                        cdbutton.disabled = false;
                        i++;

                    },
                    error: function(err){
                        // alert("Something Wen't Wrong");
                        cdbutton.disabled = false;
                    }
                });



            }, 3000);


        });


        $(document).on("click", "#stop_automatic", function(){

            $("#start_automatic").prop("disabled", false);
            clearInterval(interval);

        });

    });

    </script>
@endsection
