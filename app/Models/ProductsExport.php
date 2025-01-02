<?php

namespace App\Models;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return Product::all();
    }

    public function headings(): array
    {
        return [
            'id',
            'product_name',
            'description',
            // 'user_id',
            // 'category_id',
            // 'brand_id',
            // 'purchase',
            'unit_price',
            'unit_value',
            'unit',
            'current_stock',
            'meta_title',
            'meta_description',
            'added_by',
            'created_at',
            'updated_at',
            'slug'
        ];
    }

    /**
    * @var Product $product
    */
    public function map($product): array
    {

        $stock = $product->stocks->where("variant", null)->first();

        return [
            $product->id,
            $product->name,
            $product->description,
            // $product->user_id,
            // $product->category_id,
            // $product->brand_id,
            // $product->purchase_price,
            $product->unit_price,
            $product->unit_value,
            $product->unit,
            $product->current_stock,
            $product->meta_title,
            $product->meta_description,
            $product->user->name ?? "",
            $product->created_at,
            $product->updated_at,
            $product->slug
//            $product->current_stock,

        ];
    }
}
