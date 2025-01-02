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
class ChalProductsImport implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    private $rows = 0;

    public function collection(Collection $rows) {

        DB::beginTransaction();
        try{
            foreach ($rows as $row) {


                $approved = 1;

                $parts = explode("/", $row['image']);
                $end = end($parts);
                $file_name = explode("?", $end)[0];
                $upload = Upload::where("file_original_name", "$file_name")->where("user_id", Auth::user()->id)->first();


                $unit_value = (int) filter_var($row['unit'], FILTER_SANITIZE_NUMBER_INT);
                $unit = trim($row['unit'], " 0..9");

                $c = json_decode($row['category']);
                $catname = end($c);
                $category = Category::where("name", $catname)->where("meta_title", "muslimtownbulkcategory")->first();

                $price = filter_var($row['price'], FILTER_SANITIZE_NUMBER_INT);

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
                            'unit_price' => $price,
                            // 'purchase_price' => $row['purchase_price'] == null ? $row['unit_price'] : $row['purchase_price'],
                            'unit' => $unit,
                            'unit_value' => $unit_value,
                            "current_stock" => 100,
                            // 'meta_title' => $row['meta_title'],
                            // 'colors' => json_encode(array()),
                            // 'choice_options' => json_encode(array()),
                            // 'variations' => json_encode(array()),
                            'description' => utf8_encode($row['details']),
                            'batch_id' => 'mohammadsalim',
                            'thumbnail_img' => $upload->id ?? null,
                            'slug' => Str::slug($row['name'], '-'),
                            'published' => false,
                            'clink' => $row['link'],

                            // 'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($row['slug']))) . '-' . Str::random(5),
                            // 'photos' => $this->downloadGalleryImages($row['photos']),
                ]);


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
