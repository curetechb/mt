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
use Storage;
use DB;
use Session;

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    private $rows = 0;

    public function collection(Collection $rows) {

        DB::beginTransaction();
        try{
            foreach ($rows as $row) {
                $approved = 1;

                $parts = explode("/", $row['link']);
                $file_name = end($parts);

                $upload = Upload::where("file_name", "uploads/all/$file_name")->first();

                $unit_value = (int) filter_var($row['unit'], FILTER_SANITIZE_NUMBER_INT);
                $unit = trim($row['unit'], " 0..9");
                $category = Category::where("name", "like", "%".$row['category']."%")->first();

                $productId = Product::create([
                            'name' => $row['name'],
                            // 'description' => $row['description'],
                            'added_by' => "admin",
                            'user_id' => Auth::user()->id,
                            'approved' => $approved,
                            'category_id' => $category->id,
                            // 'brand_id' => $row['brand_id'],
                            // 'video_provider' => $row['video_provider'],
                            // 'video_link' => $row['video_link'],
                            'unit_price' => $row['unit_price'],
                            // 'purchase_price' => $row['purchase_price'] == null ? $row['unit_price'] : $row['purchase_price'],
                            'unit' => $unit,
                            'unit_value' => $unit_value,
                            // 'meta_title' => $row['meta_title'],
                            // 'meta_description' => $row['meta_description'],
                            // 'colors' => json_encode(array()),
                            // 'choice_options' => json_encode(array()),
                            // 'variations' => json_encode(array()),
                            'thumbnail_img' => $upload->id ?? null,
                            'slug' => Str::slug($row['name'], '-'),
                            'published' => true
                            // 'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($row['slug']))) . '-' . Str::random(5),
                            // 'thumbnail_img' => $this->downloadThumbnail($row['thumbnail_img']),
                            // 'photos' => $this->downloadGalleryImages($row['photos']),
                ]);
                ProductStock::create([
                    'product_id' => $productId->id,
                    'qty' => $row['current_stock'] ?? 100,
                    'price' => $row['unit_price'],
                    'variant' => '',
                ]);

                // Product Translations
                $product_translation = ProductTranslation::firstOrNew(['lang' => "en", 'product_id' => $productId->id]);
                $product_translation->name = $row['name'];
                $product_translation->unit = $row['unit'];
                $product_translation->description = "";
                $product_translation->save();

                // Product Translations
                $product_translation = ProductTranslation::firstOrNew(['lang' => "bd", 'product_id' => $productId->id]);
                $product_translation->name = $row['bengali_name'];
                $product_translation->unit = $row['unit'];
                $product_translation->description = "";
                $product_translation->save();
            }

            DB::commit();
            Session::flash("success",translate('Products imported successfully'));

        }catch(\Exception $e){
            DB::rollback();
            throw $e;

        }



    }

    public function model(array $row)
    {
        ++$this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return [
             // Can also use callback validation rules
            //  'unit_price' => function($attribute, $value, $onFailure) {
            //       if (!is_numeric($value)) {
            //            $onFailure('Unit price is not numeric');
            //       }
            //   }
        ];
    }

    public function downloadThumbnail($url){
        try {
            $upload = new Upload;
            $upload->external_link = $url;
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {

        }
        return null;
    }

    public function downloadGalleryImages($urls){
        $data = array();
        foreach(explode(',', str_replace(' ', '', $urls)) as $url){
            $data[] = $this->downloadThumbnail($url);
        }
        return implode(',', $data);
    }
}
