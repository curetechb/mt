<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExports implements FromCollection, WithMapping, WithHeadings
{

    public function collection(){
        $users = User::where('user_type','customer')->get();
        return $users;
    }

    publiC function headings():array
    {
        return [
            "Name",
            "Phone",
            "Address",
            "Number of Orders",
            "Total Amount",
        ];
    }

    /**
     *@var Order $order  */

    public function map($user): array
    {

        $address = $user->addresses()->first();
        $number_of_orders = $user->orders()->count();
        $amount = $user->orders()->where("delivery_status", "delivered")->sum("grand_total");
        $rdiscount = $user->orders()->where("delivery_status", "delivered")->sum("reward_discount");
        $cdiscount = $user->orders()->where("delivery_status", "delivered")->sum("coupon_discount");


        $results = [
            $user->name,
            $user->phone,
            $address->address ?? "",
            $number_of_orders,
            $amount - $rdiscount - $cdiscount
        ];

        return $results;


    }

}
