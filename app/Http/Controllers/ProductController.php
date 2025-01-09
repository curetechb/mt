<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductStock;
use App\Models\Category;
use App\Models\FlashDealProduct;
use App\Models\ProductTax;
use App\Models\AttributeValue;
use App\Models\Cart;
use App\Models\Color;
use App\Models\FlashDeal;
use App\Models\Shop;
use App\Models\User;
use App\Models\PriceHistory;
use Auth;
use Carbon\Carbon;
use Combinations;
use Artisan;
use Cache;
use Str;
use Session;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {

        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;

        $products = Product::where('added_by', 'admin')->where('auction_product', 0)->where('wholesale_product', 0);

        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('stocks', function($q) use ($sort_search){
                        $q->where('sku', 'like', '%'.$sort_search.'%');
                    });

        }



        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('added_by', 'seller')->where('auction_product', 0)->where('wholesale_product', 0);
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }

    public function all_products(Request $request)
    {

        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::orderBy('created_at', 'desc')->where('auction_product', 0)->where('wholesale_product', 0);
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('stocks', function($q) use ($sort_search){
                        $q->where('sku', 'like', '%'.$sort_search.'%');
                    });

        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        if($request->mt){
            $products = $products->where("clink", null);
        }

        $products = $products->paginate(15);

        $type = 'All';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        $vendors = Shop::all();
        return view('backend.product.products.create', compact('categories', 'vendors'));
    }

    public function add_more_choice_option(Request $request)
    {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();

        $html = '';

        foreach ($all_attribute_values as $row) {
            $html .= '<option value="' . $row->value . '">' . $row->value . '</option>';
        }

        echo json_encode($html);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $product = new Product();
        $product->clink = $request->clink;
        $product->name = $request->name;
        $product->is_b2b_product = $request->is_b2b_product ? true : false;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->unit_value = $request->unit_value;
        $product->unit = $request->unit;
        $product->min_qty = $request->min_qty ?: 1;
        $product->max_qty = $request->max_qty?:null;

        $tags = array();
        if ($request->tags[0] != null) {
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags = implode(',', $tags);
        $product->refundable = $request->refundable ? true : false;

        $product->thumbnail_img = $request->thumbnail_img;
        $product->banner_img = $request->banner_img;
        $product->photos = $request->photos;

        $product->unit_price = $request->unit_price;
        // if ($request->date_range != null) {
        //     $date_var               = explode(" to ", $request->date_range);
        //     $product->discount_start_date = strtotime($date_var[0]);
        //     $product->discount_end_date   = strtotime($date_var[1]);
        // }
        $product->discount = $request->discount;
        $product->discount_type     = $request->discount_type;
        $product->current_stock = $request->current_stock;
        $product->sku = $request->sku;

        $product->description = $request->description;
        $product->meta_title = $request->meta_title ?: $product->name;
        $product->meta_description = $request->meta_description ?: strip_tags($product->description);

        if ($request->has('meta_img')) {
            $product->meta_img = $request->meta_img;
        } else {
            $product->meta_img = $product->thumbnail_img;
        }


        $product->free_shipping = $request->free_shipping ? true : false;
        $product->low_stock_quantity = $request->low_stock_quantity;
        // $product->weight = $request->weight;


        $product->added_by = "admin";
        $product->user_id = Auth::user()->id;


        // $product->barcode = $request->barcode;
        // $product->stock_visibility_state = $request->stock_visibility_state;
        // $product->external_link = $request->external_link;
        // $product->external_link_btn = $request->external_link_btn;


        // $product->video_provider = $request->video_provider;
        // $product->video_link = $request->video_link;
        // $product->shipping_type = $request->shipping_type;
        // $product->est_shipping_days  = $request->est_shipping_days;


        $slug = $request->slug ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
        $same_slug_count = Product::where('slug', 'LIKE', $slug . '%')->count();
        $slug_suffix = $same_slug_count ? '-' . $same_slug_count + 1 : '';
        $slug .= $slug_suffix;

        $product->slug = $slug;


        $product->published = 1;
        if ($request->button == 'unpublish' || $request->button == 'draft') {
            $product->published = 0;
        }

        if ($request->has('featured')) {
            $product->featured = 1;
        }
        if ($request->has('todays_deal')) {
            $product->todays_deal = 1;
        }


        $product->save();

        //VAT & Tax
        // if ($request->tax_id) {
        //     foreach ($request->tax_id as $key => $val) {
        //         $product_tax = new ProductTax;
        //         $product_tax->tax_id = $val;
        //         $product_tax->product_id = $product->id;
        //         $product_tax->tax = $request->tax[$key];
        //         $product_tax->tax_type = $request->tax_type[$key];
        //         $product_tax->save();
        //     }
        // }
        //Flash Deal
        if ($request->flash_deal_id) {
            $flash_deal = FlashDeal::findOrFail($request->flash_deal_id);
            $product->discount = $request->flash_discount;
            $product->discount_type = $request->flash_discount_type;
            $product->discount_start_date = $flash_deal->start_date;
            $product->discount_end_date   = $flash_deal->end_date;
            $product->save();
        }

        $product->save();


        //Price History
        $histories = PriceHistory::create([
            "user_id" => $product->user_id ,
            "product_id"  => $product->id,
            "price" => $product->unit_price,
            "notes" => $product->description
        ]);

        // Product Translations
        $product_translation = ProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'product_id' => $product->id]);
        $product_translation->name = $request->name;
        $product_translation->unit = $request->unit;
        $product_translation->description = $request->description;
        $product_translation->save();

        Session::flash('success', translate('Product has been inserted successfully'));

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return redirect()->route("products.all");


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_product_edit(Request $request, $id)
    {

        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('digitalproducts/' . $id . '/edit');
        }

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('digitalproducts/' . $id . '/edit');
        }
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        // $categories = Category::all();
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product                    = Product::findOrFail($id);
        $product->clink = $request->clink;
        $product->name          = $request->name;
        $product->is_b2b_product = $request->is_b2b_product ? true : false;
        $product->category_id       = $request->category_id;
        $product->brand_id          = $request->brand_id;
        $product->unit_value          = $request->unit_value;
        $product->unit          = $request->unit;
        $product->min_qty                = $request->min_qty ?: 1;
        $product->max_qty                = $request->max_qty;
        $tags = array();
        if ($request->tags[0] != null) {
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags           = implode(',', $tags);
        $product->refundable = $request->refundable ? true : false;

        $product->photos                 = $request->photos;
        $product->thumbnail_img          = $request->thumbnail_img;
        $product->banner_img             = $request->banner_img;

        $product->unit_price     = $request->unit_price;
        $product->discount       = $request->discount;
        $product->discount_type     = $request->discount_type;
        $product->current_stock = $request->current_stock;
        $product->sku = $request->sku;
        $product->description   = $request->description;


        $product->description = $request->description;
        $product->meta_title = $request->meta_title ?: $product->name;
        $product->meta_description = $request->meta_description ?: strip_tags($product->description);

        if ($request->has('meta_img')) {
            $product->meta_img = $request->meta_img;
        } else {
            $product->meta_img = $product->thumbnail_img;
        }

        $slug = $request->slug ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
        $same_slug_count = Product::where('slug', 'LIKE', $slug . '%')->count();
        $slug_suffix = $same_slug_count > 1 ? '-' . $same_slug_count + 1 : '';
        $slug .= $slug_suffix;

        $product->slug = $slug;
        $product->published = 1;
        // $product->weight = $request->weight;
        // $product->barcode           = $request->barcode;
        // $product->cash_on_delivery = 0;

        $product->featured = 0;
        $product->todays_deal = 0;
        $product->is_quantity_multiplied = 0;
        $product->free_shipping = $request->free_shipping ? true : false;



        $product->low_stock_quantity     = $request->low_stock_quantity;

        // $product->stock_visibility_state = $request->stock_visibility_state;
        // $product->external_link = $request->external_link;
        // $product->external_link_btn = $request->external_link_btn;
        // $product->video_provider = $request->video_provider;
        // $product->video_link     = $request->video_link;



        // if ($request->date_range != null) {
        //     $date_var               = explode(" to ", $request->date_range);
        //     $product->discount_start_date = strtotime($date_var[0]);
        //     $product->discount_end_date   = strtotime($date_var[1]);
        // }

        // $product->shipping_type  = $request->shipping_type;
        // $product->est_shipping_days  = $request->est_shipping_days;

        if ($request->has('featured')) {
            $product->featured = 1;
        }

        if ($request->has('todays_deal')) {
            $product->todays_deal = 1;
        }

        $product->save();

        //Flash Deal
        if ($request->flash_deal_id) {
            $flash_deal = FlashDeal::findOrFail($request->flash_deal_id);
            $product->discount = $request->flash_discount;
            $product->discount_type = $request->flash_discount_type;
            $product->discount_start_date = $flash_deal->start_date;
            $product->discount_end_date   = $flash_deal->end_date;
            $product->save();
        }

        //VAT & Tax
        // if ($request->tax_id) {
        //     ProductTax::where('product_id', $product->id)->delete();
        //     foreach ($request->tax_id as $key => $val) {
        //         $product_tax = new ProductTax;
        //         $product_tax->tax_id = $val;
        //         $product_tax->product_id = $product->id;
        //         $product_tax->tax = $request->tax[$key];
        //         $product_tax->tax_type = $request->tax_type[$key];
        //         $product_tax->save();
        //     }
        // }

        //Price History
        if($request->unit_price != $product->unit_price){

            $history = PriceHistory::create([
                "user_id" => $product->user_id ,
                "product_id"  => $product->id,
                "price" => $product->unit_price,
                "notes" => $product->description
            ]);
        }



        // Product Translations
        $product_translation                = ProductTranslation::firstOrNew(['lang' => $request->lang, 'product_id' => $product->id]);
        $product_translation->name          = $request->name;
        $product_translation->unit          = $request->unit;
        $product_translation->description   = $request->description;
        $product_translation->save();

        Session::flash('success', translate('Product has been updated successfully'));

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        foreach ($product->product_translations as $key => $product_translations) {
            $product_translations->delete();
        }

        foreach ($product->stocks as $key => $stock) {
            $stock->delete();
        }

        if (Product::destroy($id)) {
            Cart::where('product_id', $id)->delete();

            Session::flash("success",translate('Product has been deleted successfully'));

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return back();
        } else {
            Session::flash("error",translate('Something went wrong'));
            return back();
        }
    }

    public function bulk_product_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $product_id) {
                $this->destroy($product_id);
            }
        }

        return 1;
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);

        if (Auth::user()->id == $product->user_id || Auth::user()->user_type == 'staff') {
            $product_new = $product->replicate();
            $product_new->slug = $product_new->slug . '-' . Str::random(5);
            $product_new->save();

            foreach ($product->stocks as $key => $stock) {
                $product_stock              = new ProductStock;
                $product_stock->product_id  = $product_new->id;
                $product_stock->variant     = $stock->variant;
                $product_stock->price       = $stock->price;
                $product_stock->sku         = $stock->sku;
                $product_stock->qty         = $stock->qty;
                $product_stock->save();
            }

            Session::flash("success",translate('Product has been duplicated successfully'));
            if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
                if ($request->type == 'In House')
                    return redirect()->route('products.admin');
                elseif ($request->type == 'Seller')
                    return redirect()->route('products.seller');
                elseif ($request->type == 'All')
                    return redirect()->route('products.all');
            } else {
                if (addon_is_activated('seller_subscription')) {
                    $seller = Auth::user()->seller;
                    $seller->remaining_uploads -= 1;
                    $seller->save();
                }
                return redirect()->route('seller.products');
            }
        } else {
            Session::flash("error",translate('Something went wrong'));
            return back();
        }
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        $product->save();
        Cache::forget('todays_deal_products');
        return 1;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;

        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription')) {
            $seller = $product->user->seller;
            if ($seller->invalid_at != null && $seller->invalid_at != '0000-00-00' && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0) {
                return 0;
            }
        }

        $product->save();
        return 1;
    }

    public function updateProductApproval(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->approved = $request->approved;

        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription')) {
            $seller = $product->user->seller;
            if ($seller->invalid_at != null && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0) {
                return 0;
            }
        }

        $product->save();
        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function updateSellerFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->seller_featured = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                foreach ($request[$name] as $key => $item) {
                    // array_push($data, $item->value);
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = Combinations::makeCombinations($options);
        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                foreach ($request[$name] as $key => $item) {
                    // array_push($data, $item->value);
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = Combinations::makeCombinations($options);
        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }



    public function priceScrapper(Request $request){

        $products = Product::where("published", true)->where("clink", "!=", null)->paginate($request->per_page ?? 500)->appends(request()->query());

        return view('backend.product.products.price-scrapper', compact('products'));

    }

    public function updatePriceScrapper(Request $request){

        $prices = $request->prices;

        foreach ($prices as $product_id => $parray) {
            $price = intval(trim($parray[0], "৳"));
            if($price){
                $product = Product::findOrFail($product_id);
                $product->unit_price = $price - 2;
                $product->save();
            }
        }

        // return redirect()->route("products.all");
        return Redirect::to($request->headers->get('referer'));
    }



    public function priceScrapper2(Request $request){


        $products = Product::where("published", true)->where("clink", "!=", null)
                    ->paginate($request->per_page ?? 500)->appends(request()->query());

        return view('backend.product.products.price-scrapper2', compact('products'));

    }

    public function updatePriceScrapper2(Request $request){

        $prices = $request->prices;

        foreach ($prices as $product_id => $cdprice) {

            if($product_id && $cdprice){
                $product = Product::findOrFail($product_id);
                $product->unit_price = $cdprice;
                $product->save();
            }
        }

        // return redirect()->route("products.all");
        return Redirect::to($request->headers->get('referer'));
    }
}
