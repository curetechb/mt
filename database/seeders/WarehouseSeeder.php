<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::insert([
            [
                "name" => "Badda Warehouse",
                "address" => "Badda Notun Bazar",
                "state_id" => 1,
                "city_id" => 1,
                "latitude" => 23.7587896,
                "longitude" => 90.3698735,
            ],
            [
                "name" => "Mirpur",
                "address" => "Mirpur Pallabi",
                "state_id" => 1,
                "city_id" => 1,
                "latitude" => 23.8281847,
                "longitude" => 90.3607134,
            ],
        ]);
    }
}
