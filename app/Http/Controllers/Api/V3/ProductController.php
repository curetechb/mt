<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\ProductIndexResource;
use App\Http\Resources\V3\ProductShowResource;
use App\Models\Category;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V3\CategoryResource;
use App\Http\Resources\V3\OnlyCategoryResource;
use App\Models\Product;
use App\Utility\CategoryUtility;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:sanctum")->only(["b2bProducts"]);
    }

    public function index(){

        $products = Product::where("published", 1)->paginate(request()->per_page);
        return ProductIndexResource::collection($products);

    }

    public function appCategoryProducts($slug){

        $category = Category::where("slug", $slug)->where("is_b2b_category", false)->first();


        $products = Product::where("category_id", $category->id)
                ->where("published", 1)->orderBy("id", "asc")->paginate(request()->per_page);

        // return ProductIndexResource::collection($products);
        return ProductIndexResource::collection($products);

    }

    public function getBreadcrumb($category){

        $breadcrumb = [];

        while($category != null){
            array_push($breadcrumb, $category);
            $category = $category->parentCategory;
        }

        return array_reverse($breadcrumb);
    }

    public function categoryProducts($slug){

        $category = Category::where("slug", $slug)->where("is_b2b_category", false)->first();

        $breadcrumb = $this->getBreadcrumb($category);

        $subcategories = Category::where("parent_id", $category->id)->where("is_b2b_category", false)->get();

        if(count($subcategories) > 0){
            return [
                "has_subcategory" => true,
                "categories" => OnlyCategoryResource::collection($subcategories),
                "breadcrumb" => $breadcrumb
            ];
        }

        $products = Product::where("category_id", $category->id)
                ->where("published", 1)->orderBy("id", "asc")->paginate(request()->per_page);

        // return ProductIndexResource::collection($products);
        return [
            "has_subcategory" => false,
            "products" => ProductIndexResource::collection($products),
            "breadcrumb" => $breadcrumb
        ];

    }

    public function b2bProducts($slug){

        $category = Category::where("slug", $slug)->where("is_b2b_category", true)->first();

        $breadcrumb = $this->getBreadcrumb($category);

        $subcategories = Category::where("parent_id", $category->id)->get();

        if(count($subcategories) > 0){
            return [
                "has_subcategory" => true,
                "categories" => OnlyCategoryResource::collection($subcategories),
                "breadcrumb" => $breadcrumb
            ];
        }

        $products = Product::where("category_id", $category->id)
                ->where("published", 1)->orderBy("rating", "desc")->paginate(request()->per_page);

        // return ProductIndexResource::collection($products);
        return [
            "has_subcategory" => false,
            "products" => ProductIndexResource::collection($products),
            "breadcrumb" => $breadcrumb
        ];

    }

    public function show($slug){

        $product = Product::where("slug", $slug)->first();
        return new ProductShowResource($product);

    }

    public function searchPorducts($query){

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
                    ->where("is_b2b_product", false)
                    ->paginate(request()->per_page ?? 10);

        return ProductIndexResource::collection($products);

    }




    // Customer API

    public function customerProducts(){
        return new ProductMiniCollection(Product::where("published", 1)
            ->orderBy("rating", "desc")->paginate(request()->per_page ?? 10));
    }

    public function topRated(){
        return new ProductMiniCollection(Product::where("published", 1)->orderBy("rating", "desc")->paginate(10));
    }

    public function category($id, Request $request)
    {
        $category_ids = CategoryUtility::children_ids($id);
        $category_ids[] = $id;

        // $products = Product::whereIn('category_id', $category_ids)->physical();
        $products = Product::where("published", 1)->whereIn('category_id', $category_ids);

        // if ($request->name != "" || $request->name != null) {
        //     $products = $products->where('name', 'like', '%' . $request->name . '%');
        // }

        return new ProductMiniCollection(filter_products($products)->get());
    }




    public function searchPorductsQuery(Request $request){

        $query = $request->q;

        $products = Product::where("published", 1)
                    ->where(function($q) use($query){

                        $q->where('name', 'like', '%' . $query . '%')
                            ->orWhere("description", 'like', '%' . $query . '%')
                            ->orWhere('tags', 'like', '%' . $query . '%');


                    })
                    ->where("is_b2b_product", false)
                    ->paginate(request()->per_page ?? 10);

        return ProductIndexResource::collection($products);

    }
}
