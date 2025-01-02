<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('INVOICE') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <style media="all">
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 0.875rem;
            font-family: '<?php echo $font_family; ?>';
            font-weight: normal;
            direction: <?php echo $direction; ?>;
            text-align: <?php echo $text_align; ?>;
            padding: 0;
            margin: 0;
        }

        .gry-color *,
        .gry-color {
            color: #000;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .25rem .7rem;
        }

        table.padding td {
            padding: .25rem .7rem;
        }

        table.sm-padding td {
            padding: .1rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: <?php echo $text_align; ?>;
        }

        .text-right {
            text-align: <?php echo $not_text_align; ?>;
        }

        .item-table tr:nth-child(even){
            background: #eee;
        }

    </style>
</head>

<body>
    <div>

        @php
            $logo = get_setting('header_logo');
        @endphp

        <div style="padding: 3rem 6rem;">
            <div>
                <table>
                    <tr>
                        <td>
                            @if ($logo != null)
                                <img src="{{ uploaded_asset($logo) }}" height="50" width="139">
                            @else
                                <img src="{{ static_asset('assets/img/footer-logo.png') }}" height="43" width="120">
                            @endif
                        </td>
                        <td style="font-size: 1rem;" class="text-right strong">মূসক-৬.৩<br>সাগুফতা টাওয়ার, হাউস- ট/১৩৬, (প্রথম ফ্লোর),
                             <br>গুলশান বাড্ডা লিংক রোড, ঢাকা ১২১২ <br> বিন : ০০৪৬৯৪১৭৮-০১০১</td>
                    </tr>
                </table>
            </div>
            <div style="display: inline-block; width: 100%;">

                <div  style="float: left; width: 50%;">
                    <table>
                        @php
                            $shipping_address = json_decode($order->shipping_address);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $shipping_address->name }}</strong>
                            </td>
                        </tr>

                        <tr>
                            <td>ডেলিভারি এলাকা: {{ $shipping_address->state }}</td>
                        </tr>

                        <tr>
                            <td>{{ $shipping_address->address }} <br/>
                            @if ($shipping_address->floor_no)
                                , ফ্লোর নং - {{ $shipping_address->floor_no }}
                            @endif
                            @if ($shipping_address->apartment)
                                , অ্যাপার্টমেন্ট - {{ $shipping_address->apartment }}
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td>মোবাইল নম্বর: {{ $order->user->phone }}{{ json_decode($order->shipping_address)->phone ? ", ".json_decode($order->shipping_address)->phone : "" }}</td>
                            <!--<td>{{ translate('Mobile') }}: {{ $order->user->phone }} {{ json_decode($order->shipping_address)->phone ?? "" }}</td>-->
                        </tr>

                        <tr>
                            <td>{{ get_setting('contact_address') }}</td>
                            <td class="text-right"></td>
                        </tr>


                    </table>
                </div>

                <div  style="float: right; width: 50%; margin-top: 45px;">
                    <table>
                        <tbody>
                            <tr>
                                <!-- <td class="gry-color small">{{ translate('Email') }}: {{ get_setting('contact_email') }}</td> -->
                                <td class="text-right small"><span
                                        class="gry-color small">শিপমেন্ট</span>
                                    <span class="strong">#{{ $order->code }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>




        <div style="padding: 0 6rem;">

            <div style="width: 100%; overflow: hidden;">
                <div style="width: 504px; overflow: hidden; margin: 0 auto;">
                    <div  style="border: 1px solid; width: 250; float: left;">
                        <table>
                            <thead>
                                <tr>
                                    <th width="10%" class="text-center">
                                        <strong>অর্ডার সময়:</strong>
                                        <br>{{ date('d M Y', $order->date) }}
                                        <br>{{ date('h:i A', $order->date) }}
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div  style="border: 1px solid; width: 250; border-left: 0px solid #fff;float: left;">
                        <table>
                            <thead>
                                <tr>
                                    <th width="10%" class="text-center">
                                        <strong>আনুমানিক ডেলিভারি সময়:</strong>

                                        @php
                                            $ordertime = new DateTime();
                                            $ordertime->setTimestamp($order->date);

                                            $t1 = new DateTime();
                                            $t1->setTime(18,0);

                                            $t2 = new DateTime();
                                            $t2->setTime(23,59);

                                            $out_of_office_hour = false;

                                            if($ordertime > $t1 && $ordertime < $t2){
                                                $out_of_office_hour = true;
                                                $ordertime = $ordertime->add(new DateInterval('P1D'));
                                                $ordertime->setTime(13,0);
                                            }

                                            $t3 = new DateTime();
                                            $t3->setTime(0,0);

                                            $t4 = new DateTime();
                                            $t4->setTime(10,0);

                                            if($ordertime > $t3 && $ordertime < $t4){
                                                $out_of_office_hour = true;
                                                // $ordertime = $ordertime->add(new DateInterval('P1D'));
                                                $ordertime->setTime(13,0);
                                            }

                                            if($out_of_office_hour == false){
                                                $hours = 3;
                                                $ordertime->add(new DateInterval("PT{$hours}H"));
                                            }
                                        @endphp
                                        <!--<br>{{ date('d M Y', $order->date) }}-->
                                        <!--<br>{{ date('h:i A', strtotime('+2 hours', $order->date)) }}-->
                                        <br>{{ $ordertime->format('d M Y') }}
                                        <br>{{ $ordertime->format('h:i A') }}
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div style="margin-top: 10px">
                <table  border="1" style="border-collapse: collapse">
                    <thead style="background: #555">
                        <tr style="color: white;background: #555">
                            <th width="10%" class="text-center" style="color:white;font-weight: 600;text-align: center;">সিরিয়াল</th>
                            <th width="55%" class="text-center" style="color:white;font-weight: 600;">নাম</th>
                            <th width="15%" class="text-center" style="color:white;font-weight: 600;">মূল্য</th>
                            {{-- <th width="15%" class="text-center" style="color:white;font-weight: 600;">
                                {{ translate('Discount Price') }}</th> --}}
                            <th width="5%" class="text-center" style="color:white;font-weight: 600;">পরিমাণ</th>
                            <!-- <th width="10%" class="text-center">{{ translate('Tax') }}</th> -->
                            <th width="15%" class="text-right" style="color:white;font-weight: 600;text-align: center;">মোট <span
                                    style="font-size:8px;">(ভ্যাট সহ)</span></th>
                        </tr>
                    </thead>
                    <tbody class="strong">
                        @foreach ($order->orderDetails as $key => $orderDetail)
                            @if ($orderDetail->product != null)
                                <tr class="">
                                    <td style="text-align: center;">{{ $loop->index + 1 }}</td>
                                    <td>{{ $orderDetail->product->getTranslation('name', 'bd') }}
                                        {{ $orderDetail->product->unit_value > 0 ? $orderDetail->product->unit_value : "" }} {{ $orderDetail->product->unit ?? "" }}
                                        @if ($orderDetail->variation != null)
                                            ({{ $orderDetail->variation }})
                                        @endif
                                    </td>
                                    <td class="currency" style="text-align: right">
                                        {{ single_price($orderDetail->price / $orderDetail->quantity) }}</td>
                                    {{-- <td class="currency" style="text-align: right">
                                        {{ single_price($orderDetail->tax / $orderDetail->quantity) }}</td> --}}
                                    <td style="text-align: right">{{ $orderDetail->quantity }}</td>
                                    <td class="currency" style="text-align: right">
                                        {{ single_price($orderDetail->price) }}</td>
                                    <!-- <td class="text-right currency">{{ single_price($orderDetail->price + $orderDetail->tax) }}</td> -->
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

                <table class="text-right sm-padding small strong">
                    <tbody>
                        <tr>
                            <td width="10%"></td>
                            <td width="55%"></td>
                            <td colspan="2" width="20%" class="gry-color text-left">সাবটোটাল:</td>
                            <td width="15%" class="currency" style="background: #eee">
                                {{ single_price($order->orderDetails->sum('price')) }}</td>
                        </tr>
                        <tr>
                            <td width="10%"></td>
                            <td width="55%"></td>
                            <td colspan="2" width="20%" class="gry-color text-left">শিপিং:</td>
                            <td width="15%" style="background: #eee">
                                {{ single_price($order->shipping_cost) }}
                            </td>
                        </tr>
                        <!-- <tr class="border-bottom">
<th class="gry-color text-left">{{ translate('Total Tax') }}</th>
<td class="currency">{{ single_price($order->orderDetails->sum('tax')) }}</td>
</tr> -->

                        <tr>
                            <td width="10%"></td>
                            <td width="55%"></td>
                            <td colspan="2" width="20%" class="gry-color text-left">ডিসকাউন্ট:</td>
                            <td width="15%" style="background: #eee">
                                {{ single_price($order->coupon_discount + $order->reward_discount) }}
                            </td>
                        </tr>
                        @php
                            $emergency_order = \App\Models\Order::where("user_id", $order->user_id)->where("id", "!=", $order->id)->where("is_emergency_order", true)->where("payment_status", "!=", "paid")->first();
                        @endphp
                        @if ($emergency_order)
                            @php
                                $order_total = $order->grand_total - $order->coupon_discount - $order->reward_discount;
                                $previous_due = $emergency_order->grand_total - $emergency_order->coupon_discount - $emergency_order->reward_discount;
                            @endphp
                            <tr>
                                <td width="10%"></td>
                                <td width="55%"></td>
                                <td colspan="2" width="20%" class="text-left strong">
                                    <strong>আগের বকেয়া</strong>
                                </td>
                                <td width="15%" class="currency" >
                                    <strong>{{ single_price($previous_due) }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%"></td>
                                <td width="55%"></td>
                                <td colspan="2" width="20%" class="text-left strong">
                                    <strong>নগদ সংগ্রহ করতে হবে*:</strong>
                                </td>
                                <td width="15%" class="currency" >
                                    <strong>{{ single_price($order_total + $previous_due) }}</strong>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td width="10%"></td>
                                <td width="55%"></td>
                                <td colspan="2" width="20%" class="text-left strong">
                                    <strong>নগদ সংগ্রহ করতে হবে*:</strong>
                                </td>
                                <td width="15%" class="currency" >
                                    <strong>{{ single_price($order->grand_total - $order->coupon_discount - $order->reward_discount) }}</strong>
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>

        </div>

        <div style="padding:0 6rem;">

            <br>
            <br>
            <hr>
            <p style="font-size: 12px">তাজা বাজার থেকে অর্ডার করার জন্য আপনাকে ধন্যবাদ।
                আমরা অপচনশীল দ্রব্যের জন্য ৭-দিনের রিটার্ন/রিফান্ড নীতি এবং পচনশীলদ্রব্যের জন্য ১-দিনের রিটার্ন/ফেরত নীতি অফার করি। এই অর্ডার সম্পর্কে
                আপনার কোন অভিযোগ থাকলে, অনুগ্রহ করে আমাদেরকে +৮৮০৯৬৪২-৭৭৮৭৭৮ নম্বরে কল করুন অথবা  support@muslim.town এ ইমেল করুন।
                *মোট হল ভ্যাট সহ (GO ০২/মূসক/২০১৯ অনুযায়ী ভ্যাট গণনা করা হয়েছে)</p>
            <p style="font-size: 12px">এটি একটি সিস্টেম উ ৎপন্ন চালান এবং কোন স্বাক্ষর বা সীল প্রয়োজন হয় না।</p>
        </div>
</body>

</html>
