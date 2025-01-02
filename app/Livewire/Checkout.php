<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Product;
use Auth;
use DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderDetail;

class Checkout extends Component
{

    public $phone_number = '';
    public $name = '';
    public $address = '';
    public $payment_method = 'cash_on_delivery';


    public $shippingCost = 0;
    public $totalCost = 0;
    public $temp_id = null;
    public $general_error = "";

    public function mount(){

        $this->initializeData();

    }

    public function initializeData()
    {
        $cart_total = $this->getCartTotal();
        $shipping_cost = get_setting("flat_rate_shipping_cost");

        $this->shippingCost = currency_symbol().$shipping_cost;
        $this->totalCost = currency_symbol().($cart_total + $shipping_cost);

    }

    public function render()
    {
        return view('livewire.checkout');
    }

    public function getCartTotal(){

        $temp_id = \Cookie::get("temp_id");
        $q = DB::table('carts')
            ->join('products', 'products.id', '=', 'carts.product_id')
            ->select(DB::raw('sum(carts.quantity * products.unit_price) as total'));

        $q = $q->where("carts.temp_user_id", $temp_id);

        $cart_totals = $q->pluck("total")->toArray();

        return array_sum($cart_totals);
    }

    public function placeOrder()
    {
        $this->general_error = "";

        $validated = $this->validate([ 
            'phone_number' => 'required|min:11|max:11',
            'name' => 'required|min:3|max:100',
            'address' => 'required|min:3|max:200',
        ]);

        $temp_id = \Cookie::get("temp_id");
        $carts = Cart::where('temp_user_id',  $temp_id)->get();

        if (count($carts) <= 0) {
            $this->general_error = "Your cart is empty!";
            return;
            // dd("Cart is empty");
        }

        $address = Address::where('phone', $this->phone_number)->first();
        
        if (!$address) {
            $address = new Address;
            $address->address = $this->address;
            $address->name = $this->name;
            $address->phone = $this->phone_number;
            $address->set_default = 1;
            $address->save();
        }

        $shippingAddress = [];
        $shippingAddress['name']        = $address->name;
        $shippingAddress['address']     = $address->address;
        $shippingAddress['phone']       = $this->phone_number;


        DB::beginTransaction();
        try{

            $order = new Order;
            $order->ordered_from = "web";
            $order->tracking_code = rand(1000, 9999);
            $order->shipping_address = json_encode($shippingAddress);
            $order->shipping_type = "delivery";
            $order->payment_type = $this->payment_method;
            $order->date = strtotime('now');
            $order->save();

            foreach ($carts as $cartItem){

                $product = Product::find($cartItem['product_id']);

                $order_detail = new OrderDetail();
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;

                $order_detail->price = $product->unit_price * $cartItem['quantity'];
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];

                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();


                $product->current_stock -= $cartItem['quantity'];
                $product->num_of_sale += $cartItem['quantity'];
                $product->save();

            }

            $cart_total = $this->getCartTotal();
            $shipping = get_setting('flat_rate_shipping_cost'); // default cost 50
            $grand_total = $cart_total + $shipping;

            $order->grand_total = $grand_total;
            $order->shipping_cost = $shipping;

            $order_code = "100001";
            $order_code_len = strlen($order_code);
            $order_id_len = strlen("$order->id");
            $new_order_code = $order->id;

            if($order_id_len < $order_code_len){
                $order_code = substr($order_code, 0, $order_code_len - $order_id_len);
                $new_order_code = $order_code.$order->id;
            }

            $order->code = $new_order_code;


            $order->save();



            // NotificationUtility::sendOrderPlacedNotification($order);
            // session()->put("order_id", $order->id);
            Cart::where('user_id', $this->temp_id)->delete();

            DB::commit();

            $this->redirect("/purchased/$order->id"); 

        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function findPhoneInDb(){

        $this->resetErrorBag();

        if(strlen($this->phone_number) >=11){
            $address = Address::where('phone', $this->phone_number)->first();
            if($address){
                $this->name = $address->name;
                $this->address = $address->address;
            }
        }

    }
}
