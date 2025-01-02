<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\Product;

class SearchController extends Controller
{
    public function suggestions($query)
    {
        $products = Product::where("published", 1)
            ->where(function($q) use($query){

                $parts = explode(',', trim($query));
                $newparts = [];
                foreach ($parts as $part) {
                    if($part != ""){
                        array_push($newparts, $part);
                    }
                }

                $q->where('name', 'like', '%' . $newparts[0] . '%')
                    ->orWhere("description", 'like', '%' . $newparts[0] . '%')
                    ->orWhere('tags', 'like', '%' . $newparts[0] . '%');

                for($i = 1; $i < count($newparts); $i++){
                    $q->orWhere('name', 'like', '%' . $newparts[$i] . '%')
                        ->orWhere("description", 'like', '%' . $newparts[$i] . '%')
                        ->orWhere('tags', 'like', '%' . $newparts[$i] . '%');
                }

            })
            ->get(["name", "slug"])->take(10);


        return response([
            "success" => true,
            "data" => count($products) > 0 ? $products : [["name" => "Not Found", "slug" => "_not_found_"]],
        ], 200);
    }
}
