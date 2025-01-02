<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Auth;
use DB;
use Storage;
use Session;

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class ProductImportUpdate implements ToCollection, WithHeadingRow, WithValidation
{

    public function collection(Collection $rows) {

        DB::beginTransaction();
        try{
            foreach ($rows as $row) {

                if(!$row["id"]) break;

                $product = Product::find($row['id']);

                $slug = $row['slug'] ? Str::slug($row['slug'], '-') : Str::slug($row['slug'], '-');
                $same_slug_count = Product::where('slug', 'LIKE', $slug . '%')->count();
                $slug_suffix = $same_slug_count > 1 ? '-' . $same_slug_count + 1 : '';
                $slug .= $slug_suffix;

                $product->update([
                    'name' => $row['product_name'],
                    'description' => $row['description'],
                    'unit_price' => $row['unit_price'],
                    'current_stock' => $row['current_stock'],
                    // 'purchase_price' => $row['purchase_price'] == null ? $row['unit_price'] : $row['purchase_price'],
                    'unit' => $row['unit'],
                    'meta_title' => $row['meta_title'],
                    'meta_description' => $row['meta_description'],
                    'slug' => $slug,
                ]);

            }

            DB::commit();
            Session::flash("success",translate('Products Updated successfully'));
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }
 

    public function rules(): array
    {
        return [
             // Can also use callback validation rules
             'unit_price' => function($attribute, $value, $onFailure) {
                  if (!is_numeric($value)) {

                        Session::flash("error",translate('Unit price Invalid'));

                       $onFailure('Unit price is not numeric');
                  }
              }
        ];
    }


}
