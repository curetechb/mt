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
    <div class="row">

        <div class="card col-12">
            <div class="card-header">
                <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
            </div>
            <div class="card-body">


                <div class="row gutters-5 mb-3">
                    <div class="col text-center text-md-left">
                    </div>

                    <div class="col-12 col-md-2 ">
                        <label for="invoice_number">{{ translate('Invoice Number') }}</label>
                        <input type="text" name="invoice_number" class="form-control" id="invoice_number">
                        {{-- <small>Leave it blank to generate automatically</small> --}}
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="customer_list">{{ translate('Customer List') }}</label>
                        {{-- <select class="js-data-example-ajax" name="product_id" required style="width: 100%"></select> --}}
                        <div class="select-div">
                            <select name="customer" id="customer" class="form-control select2" style="width: 100%">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="customer_list">{{ translate('Sales Time') }}</label>
                        <input type="datetime-local" name="sale_time" class="form-control" id="sale_time">
                    </div>
                </div>


                <form id="product_form">
                    <div class="row">

                        <div class="col-md-4">
                            <label for="product_list">{{ translate('Product List') }}</label>
                            <select class="js-data-example-ajax" name="product_id" required style="width: 100%"></select>
                        </div>

                        <!-- Button trigger modal -->
                        <div class="col ml-auto mt-3">
                            <div>
                                <button type="submit" class="btn btn-primary" style="margin-top: 10px">
                                    {{ translate('Add') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>



                <hr class="new-section-sm bord-no">
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-trans-dark">
                                    <th width="10%">{{ translate('Photo') }}</th>
                                    <th class="text-uppercase">{{ translate('Description') }}</th>
                                    <th data-breakpoints="lg" class="min-col text-center text-uppercase">
                                        {{ translate('Qty') }}</th>
                                    <th data-breakpoints="lg" class="min-col text-center text-uppercase">
                                        {{ translate('Price') }}</th>
                                    <th data-breakpoints="lg" class="min-col text-center text-uppercase">
                                        {{ translate('Total') }}</th>
                                    <th data-breakpoints="lg" class="min-col text-left text-uppercase">
                                        {{ translate('Options') }}</th>
                                </tr>
                            </thead>
                            <tbody id="product_body">

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" cols="30" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="order_status">Order Status</label>
                            <select name="order_status" id="order_status" class="form-control">
                                <option value="pending">{{ translate('Pending') }}</option>
                                <option value="processing">{{ translate('Processing') }}</option>
                                <option value="on_the_way">{{ translate('On The Way') }}</option>
                                <option value="next_day">{{ translate('Next Day') }}</option>
                                <option value="delivered">{{ translate('Delivered') }}</option>
                                <option value="cancelled">{{ translate('Canceled') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="discount">Discount</label>
                            <input type="number" name="discount" class="form-control" id="discount">
                        </div>
                        <div class="mb-3">
                            <label for="shipping">Shipping</label>
                            <input type="number" name="shipping" class="form-control" id="shipping">
                        </div>
                    </div>
                </div>

                <div class="clearfix float-right">
                    <table class="table">
                        <tbody>

                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Shipping') }} :</strong>
                                </td>
                                <td id="showshipping">
                                    0
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Discount') }} :</strong>
                                </td>
                                <td>
                                    <span class="text-danger" id="showdiscount">-0</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('TOTAL') }} :</strong>
                                </td>
                                <td class="text-muted h5" id="showtotal">
                                    0
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>


            </div>
            <div class="row mb-3 mr-2">
                <div class="col-md-4 ml-auto">
                    <div class="alert alert-danger d-none" role="alert" id="response_error">

                    </div>
                    <button class="btn btn-primary btn-block" id="create_order">Create Order</button>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {

            $(".select2").select2();

            $('.js-data-example-ajax').select2({
                placeholder: "Select Product",
                ajax: {
                    url: '/api/search-products',
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    dataType: 'json',
                    processResults: function(data) {
                        var res = data.data.map(function(item) {
                            return {
                                id: item.slug,
                                text: item.name
                            };
                        });
                        return {
                            results: res
                        };
                    }
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                }
            });


            $("#product_form").on("submit", function(e) {

                e.preventDefault();
                var form = $("#product_form").serialize();

                var slug = form.split("=")[1];

                $.ajax({
                    type: "GET",
                    url: "/api/product/" + slug,
                    dataType: "json",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    success: function(response) {

                        var product = response.data;
                        var price = parseInt(product.unit_price.replace("৳", ""))

                        var html = "<tr>";
                        html +=
                            `<td class='img'><img src='${product.image}' alt='image' width='50'/></td>`;
                        html += `<td class='name'>${product.name}</td>`;
                        html +=
                            `<td class='qty'> <input type='number' name='qty' value='1' class='form-control pqty' data-id='${product.id}'/> </td>`;
                        html += `<td class='price'>${product.unit_price}</td>`;
                        html += `<td class='total'>৳${price}</td>`;
                        html +=
                            `<td class='destroy'><a href='' class='btn btn-xs btn-danger pdetele'>Delete</a></td></tr>`;

                        $("#product_body").append(html);

                        setTimeout(() => {
                            updateTotal();
                        }, 1000);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });

            });

            $(document).on("click", ".pdetele", function(e) {

                e.preventDefault();
                $(this).parent().parent().remove();
                updateTotal();

            });

            $(document).on("keyup", ".pqty", function(e) {

                e.preventDefault();
                var qty = parseInt(e.target.value);
                if (!qty) return false;
                var price = parseInt($(this).parent().siblings(".price").text().replace("৳", ""))
                var total = price * qty;

                $(this).parent().siblings(".total").html("৳" + total);

                updateTotal();
            });


            $(document).on("keyup", "#discount", function(e) {

                $("#showdiscount").text("৳" + e.target.value)
                updateTotal();

            });

            $(document).on("keyup", "#shipping", function(e) {

                $("#showshipping").text("৳" + e.target.value)
                updateTotal();

            });


            function updateTotal() {

                var totals = $(".total");

                var total = 0;
                var discount = 0;
                var shipping = 0;

                for (let i = 0; i < totals.length; i++) {
                    let v = totals[i].innerText.replace("৳", "");
                    if (v) {
                        v = parseInt(v);
                        total += v;
                    }
                }

                discount = $("#discount").val();
                shipping = $("#shipping").val();

                if (discount) discount = parseInt(discount);
                if (shipping) shipping = parseInt(shipping);

                total = total - discount + shipping;
                $("#showtotal").text("৳" + total);
            }



            $(document).on("click", "#create_order", function(e) {

                const discount = $("#discount").val();
                const shipping = $("#shipping").val();
                const order_status = $("#order_status").val();
                const notes = $("#notes").val();

                const invoice_number = $("#invoice_number").val();
                const customer = $("#customer").val();
                const sale_time = $("#sale_time").val();

                const product_elems = $(".pqty");

                const products = [];

                for (let i = 0; i < product_elems.length; i++) {
                    var id = product_elems[i].getAttribute("data-id");
                    var qty = product_elems[i].value;

                    products.push({
                        id: id,
                        quantity: qty
                    });
                }

                // console.log(discount, shipping, order_status, notes, invoice_number, customer, sale_time, products);

                var data = {
                    discount: discount,
                    shipping: shipping,
                    order_status: order_status,
                    notes: notes,
                    invoice_number: invoice_number,
                    customer: customer,
                    sale_time: sale_time,
                    products: products
                };

                $("#create_order").prop("disabled", true);

                $.ajax({
                    type: "post",
                    url: "{{ route('newsale.store') }}",
                    data: JSON.stringify(data),
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        console.log(response.data);
                        AIZ.plugins.notify('success', 'Sale Created Successfully');
                        setTimeout(function() {
                            window.location.href = "{{ route('b2b_orders.index') }}";
                        }, 1000);

                    },
                    error: function(err) {
                        $("#create_order").prop("disabled", false);
                        console.log(err);
                        if (err.status == 422) {
                            $("#response_error").removeClass("d-none");
                            $("#response_error").text(err.responseJSON.message);
                        }
                    }
                });
            });

        });
    </script>
@endsection
