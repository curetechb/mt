<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Livewire\Component;
use Auth;
use DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class CartSidebar extends Component
{

    public $path = "";
    public $cartItems = [];
    public $cartTotal = 0;
    public $cartCount = 0;
    public $shippingCost = 0;
    public $totalCost = 0;
    public $temp_id = null;

    public function mount(){

        $temp_id = \Cookie::get("temp_id");
        if(!$temp_id){
            $temp_id = Str::random(16);
            \Cookie::queue(\Cookie::forever("temp_id", $temp_id));
        }
        $this->path = request()->path();
        $this->temp_id = $temp_id;
        $this->initializeData();

    }

    public function initializeData()
    {

        $carts = Cart::where("temp_user_id", $this->temp_id)->get();

        $cart_total = $this->getCartTotal();
        $shipping_cost = get_setting("flat_rate_shipping_cost");

        $this->cartTotal = currency_symbol().$cart_total;
        $this->shippingCost = currency_symbol().$shipping_cost;
        $this->cartItems = $carts;
        $this->cartCount = count($carts);
        $this->totalCost = currency_symbol().($cart_total + $shipping_cost);


    }

    public function render()
    {
        return view('livewire.cart-sidebar');
    }

    public function getCartTotal(){

        $q = DB::table('carts')
            ->join('products', 'products.id', '=', 'carts.product_id')
            ->select(DB::raw('sum(carts.quantity * products.unit_price) as total'));


        $q = $q->where("carts.temp_user_id", $this->temp_id);

        $cart_totals = $q->pluck("total")->toArray();

        return array_sum($cart_totals);
    }


    #[On('cart-updated')]
    public function update($product_id, $type, $open_sidebar = false){

        // dd($product_id, $type);



        $cartItem = Cart::where("temp_user_id", $this->temp_id)->where("product_id", $product_id)->first();

        $product = Product::findOrFail($product_id);

        if(!$cartItem){

            $cartItem = Cart::create([
                "temp_user_id" => $this->temp_id,
                "product_id" => $product_id,
                "quantity" => $product->min_qty
            ]);

        }else{

            if($type == "+" && $product->current_stock <= $product->low_stock_quantity){
                $this->dispatch("product_error", "Product out of Stock");
                return;
            }

            if($type == "+" && $product->max_qty && $cartItem->quantity >= $product->max_qty){

                $this->dispatch("product_error", "Your Desired Quantity is Not available for this product");
                return;

            }

            if($type == "-"){
                if($cartItem->quantity <= $product->min_qty){
                    $cartItem->delete();
                }else if($cartItem->quantity > 1){
                    $cartItem->quantity = $cartItem->quantity - 1;
                    $cartItem->save();
                }
            }


            if($type == "+"){
                $cartItem->quantity = $cartItem->quantity + 1;
                $cartItem->save();
            }

        }

        $this->initializeData();
        $this->dispatch('refresh-welcome');
        if($open_sidebar ==  true){
            $this->redirect("/checkout");
        }
    }

    public function destroy($product_id){

        $cartItem = Cart::where("temp_user_id", $this->temp_id)->where("product_id", $product_id)->first();

        $cartItem->delete();

        $this->initializeData();
        $this->dispatch('refresh-welcome');
    }
}
