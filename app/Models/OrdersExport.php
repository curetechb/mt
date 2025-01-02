<?php

namespace App\Models;

use App\Models\Order;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithMapping, WithHeadings
{
    protected $orders;
    protected $index = 1;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection(){
        // $now = Carbon::now();
        // if(request('day')){
        //     $now = Carbon::now()->subDays(request('day'));
        // }

        // $order = Order::whereDate('created_at', $now);
        // if(request()->delivery_status){
        //     $order = $order->where("delivery_status", request("delivery_status"));
        // }
        // return $order->get();
        // $orderIds = $this->orders->pluck("id")->toArray();
        // return OrderDetail::whereIn("order_id", $orderIds)->get();
        return $this->orders->get();
    }

    publiC function headings():array
    {
        return [
            "Serial",
            "Date",
            "Order No",
            "Customer Name",
            "Customer Contact No",
            "Location",
            "Product Code",
            "Product Name",
            "Unit Price",
            "Quantity",
            "Price",
            "Total Price",
            "Coupon Discount",
            "Reward Discount",
            "Final Total",
            "Refund",
            "Delivery Man",
            "Delivery Time",
            "Delivery Status",
            "Cancel Reason"
        ];
    }

    /**
     *@var Order $order  */

    public function map($order): array
    {

        $rows = [];

        $delivery_boy = $order->delivery_boy ? $order->delivery_boy->name : "";

        foreach ($order->orderDetails as $i => $orderDetail) {

            $customer = json_decode($order->shipping_address);

            $refund = RefundRequest::where("order_detail_id", $orderDetail->id)->where("admin_approval", true)->first();

            array_push($rows, [
                $i == 0 ? $this->index : "",
                $i == 0 ? $order->created_at : "",
                $i == 0 ? $order->code : "",
                $i == 0 ? $customer->name : "",
                $i == 0 ? $customer->phone ?: Auth::user()->phone : "",
                $i == 0 ? $customer->address. ",". $customer->state : "",
                str_pad($orderDetail->product_id, 6, "0", STR_PAD_LEFT),
                $orderDetail->product->name ?? "",
                $orderDetail->price / $orderDetail->quantity,
                $orderDetail->quantity,
                $orderDetail->price,
                $i == 0 ? $order->grand_total : "",
                $i == 0 ? $order->coupon_discount : "",
                $i == 0 ? $order->reward_discount : "",
                $i == 0 ? $order->grand_total - $order->coupon_discount - $order->reward_discount : "",
                $i == 0 ? $refund->refund_amount ?? "" : "",
                $i == 0 ? $delivery_boy : "",
                $i == 0 ? $order->delivery_time : "",
                $i == 0 ? $order->delivery_status : "",
                $i == 0 ? $order->reason : "",
            ]);

        }

        $this->index++;

        return $rows;

    }
}
