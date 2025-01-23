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
        }

        $address->address = $this->address;
        $address->name = $this->name;
        $address->phone = $this->phone_number;
        $address->set_default = 1;
        $address->save();

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

            if($this->payment_method == "cash_on_delivery"){
                $this->redirect("/purchased/$order->id"); 
            }else{
                $this->redirectToOnlinePayment($order);
            }

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

    public function updatePaymentMethod($value)
    {
        $this->payment_method = $value;
    }

    public function redirectToOnlinePayment($order){

        /* PHP */
        $post_data = array();
        $post_data['store_id'] = "curetechbd0live";
        $post_data['store_passwd'] = "66B346740CE5152037";
        $post_data['total_amount'] = $order->grand_total;
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = "SSLCZ_LIVE_".uniqid();
        
        $post_data['success_url'] = route('sslcommerz.status', ['order_status' => 'success', 'order_id' => $order->id]);
        $post_data['fail_url'] = route('sslcommerz.status', ['order_status' => 'failed', 'order_id' => $order->id]);
        $post_data['cancel_url'] = route('sslcommerz.status', ['order_status' => 'cancel', 'order_id' => $order->id]);
        # $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE

        # EMI INFO
        $post_data['emi_option'] = "0";
        // $post_data['emi_max_inst_option'] = "9";
        // $post_data['emi_selected_inst'] = "9";

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $this->name;
        // $post_data['cus_email'] = "h.salim@curetechbd.com";
        $post_data['cus_add1'] = $this->address;
        // $post_data['cus_add2'] = "Greenville";
        // $post_data['cus_city'] = "Dhaka";
        // $post_data['cus_state'] = "Greenville";
        // $post_data['cus_postcode'] = "1000";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $this->phone_number;
        // $post_data['cus_fax'] = "864-249-0312";

        # SHIPMENT INFORMATION
        $post_data['shipping_method'] = "NO";
        // $post_data['ship_name'] = "Store Test";
        // $post_data['ship_add1 '] = "Dhaka";
        // $post_data['ship_add2'] = "Dhaka";
        // $post_data['ship_city'] = "Dhaka";
        // $post_data['ship_state'] = "Dhaka";
        // $post_data['ship_postcode'] = "1000";
        // $post_data['ship_country'] = "Bangladesh";

        # OPTIONAL PARAMETERS
        // $post_data['value_a'] = "ref001";
        // $post_data['value_b '] = "ref002";
        // $post_data['value_c'] = "ref003";
        // $post_data['value_d'] = "ref004";

        # Product Info
        // $post_data['num_of_item']       = '1';
        // $post_data['product_name']      = 'Web Design';
        // $post_data['product_category']  = 'digital product';
        // $post_data['product_profile']   = 'non-physical-goods';

        # CART PARAMETERS
        // $post_data['cart'] = json_encode(array(
        //     array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
        //     array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
        //     array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
        //     array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
        // ));

        // $post_data['product_amount'] = "100";
        // $post_data['vat'] = "5";
        // $post_data['discount_amount'] = "5";
        // $post_data['convenience_fee'] = "3";


        # REQUEST SEND TO SSLCOMMERZ
        $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }

        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );

        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
           
            return redirect()->to($sslcz['GatewayPageURL']);

        } else {
            $this->dispatch("product_error", "Something wen't wrong!");
            return;
        }


    }
}
