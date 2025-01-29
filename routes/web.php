<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AizUploadController;
use App\Http\Controllers\Api\V3\PublicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BkashController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\CartAnalysisController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CustomerBulkUploadController;
use App\Http\Controllers\CustomerCancelListController;
use App\Http\Controllers\CustomerComplainController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerFeedbackController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\CustomerProductController;
use App\Http\Controllers\DeliveryBoyController;
use App\Http\Controllers\DummyController;
use App\Http\Controllers\FlashDealController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PickupPointController;
use App\Http\Controllers\PriceHistoryController;
use App\Http\Controllers\ProductBulkUploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\StoryGroupController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TextToBmpController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\UserActivityHistoryController;
use App\Http\Controllers\WarehouseController;
use App\Livewire\Checkout;
use App\Livewire\ECard;
use App\Livewire\Purchased;
use App\Livewire\ShowPage;
use App\Livewire\SupportPage;
use App\Livewire\WelcomePage;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get("/", [LoginController::class, 'showLoginForm'])->middleware("guest");
Route::get('/', WelcomePage::class);
Route::get('/page/{slug}', ShowPage::class);
Route::get('/checkout', Checkout::class);
Route::get('/purchased/{order_id}', Purchased::class);
Route::get('/support', SupportPage::class);
Route::get('/e-card', ECard::class);

Route::get('/img', function () {
    return view('text_to_bmp');
});

Route::post('/convert-to-bmp', [TextToBmpController::class, 'convertToBmp'])->name('convert-to-bmp');
Route::get('/di', [TextToBmpController::class, 'createImageWithText']);

// Route::get('salim', function () {
//     // 01732119526
//     $array = array(
//         array("val0" => "001", "product_id" => 2, "qty" => 1, "val1" => "Monir Hossain", "val2" => "01633950636", "val3" => "25/11/2024", "val4" => "koral Pink Mug x 1", "val5" => "MCC Tower Level 7. House 76.Road 127.Gulshan Avenue. Gulshan-1 Dhaka.", "val6" => "460", "val7" => "Pickup", "val8" => "Friday Delivery Needed(29.11.24)"),
//         array("val0" => "002", "product_id" => 2, "qty" => 1, "val1" => "Sazzad", "val2" => "01723313735", "val3" => "26/11/2025", "val4" => "koral pink mug x1", "val5" => "Puraton bahadur bajar, dinajpur", "val6" => "480", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "003", "product_id" => 2, "qty" => 1, "val1" => "Dr. Shetu", "val2" => "01300157359", "val3" => "26/11/2026", "val4" => "Koral Pink Mug x 1", "val5" => "check post, rangpur", "val6" => "480", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "004", "product_id" => 3, "qty" => 1, "val1" => "Mohammad Redwan", "val2" => "01333393558", "val3" => "26/11/2024", "val4" => "Oppo A38 X1 (Caligraphy)", "val5" => "Hemayet pur, joyna bari madrasa road", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "007", "product_id" => 3, "qty" => 1, "val1" => "Belal Hosain", "val2" => "01756641181", "val3" => "26/11/2024", "val4" => "Vivo y 11 x1", "val5" => "Tazumuddin Haque Road, Bhola", "val6" => "300", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "008", "product_id" => 3, "qty" => 2, "val1" => "Tahmina zahan", "val2" => "01706114609", "val3" => "26/11/2024", "val4" => "Vivo y36 (phone cover x 2)", "val5" => "Feni mohipal temuhani", "val6" => "550", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "009", "product_id" => 3, "qty" => 1, "val1" => "Qari Monir Hossein", "val2" => "01884283124", "val3" => "26/11/2024", "val4" => "vivo y15s x 1 (Proud Muslim)", "val5" => "Nangalkot Police Station, Volain Bazar", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "010", "product_id" => 3, "qty" => 1, "val1" => "Siam Admad", "val2" => "1967152807", "val3" => "26/11/2024", "val4" => "Redmi13", "val5" => "Moulvibazar,Juri", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "012", "product_id" => 3, "qty" => 1, "val1" => "Shaid Ahmad", "val2" => "1925179230", "val3" => "26/11/2024", "val4" => "Samsung a35", "val5" => "Chander kandi, Raipura, Narsindi", "val6" => "300", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "013", "product_id" => 3, "qty" => 1, "val1" => "Md Touhid", "val2" => "01804665531", "val3" => "26/11/2024", "val4" => "samsung galaxy A04S x 1(calligraphy )", "val5" => "TIC colony, Sector 4, Uttara", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "014", "product_id" => 3, "qty" => 1, "val1" => "MD Safwaan Saad", "val2" => "01753545456", "val3" => "27/11/2024", "val4" => "Redmi note 10 pro cover X 1 (Caligraphy)", "val5" => "House-2, Rd-9, Block-C, Bosila City Developer Housing(near bosila bridge), Mohammadpur, Dhaka", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "015", "product_id" => 5, "qty" => 3, "val1" => "Faria Islam", "val2" => "01711815400", "val3" => "27/11/2024", "val4" => "Sweat Jumper X 3 ( S, 2M)", "val5" => "Curetech Ltd., Shagufta Tower", "val6" => "1500", "val7" => "Delivered", "val8" => "Received"),
//         array("val0" => "016", "product_id" => 5, "qty" => 1, "val1" => "Tasmin Ahmed", "val2" => "01722065374", "val3" => "27/11/2024", "val4" => "Sweater jumper, size L", "val5" => "Shagufta tower, Curetechbd,  Gulshan Badda Link road", "val6" => "500", "val7" => "Delivered", "val8" => "Received"),
//         array("val0" => "017", "product_id" => 3, "qty" => 1, "val1" => "Nafis Noor", "val2" => "01876856860", "val3" => "27/11/2024", "val4" => "Redmi Note-9 (Proud Muslim)", "val5" => "Keraniganj,Dhaka", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "018", "product_id" => 5, "qty" => 2, "val1" => "Apple Mahmud", "val2" => "01726014285", "val3" => "27/11/2024", "val4" => "Sweater jumper x 2, size L and XL", "val5" => "Shagufta tower, Curetechbd,  Gulshan Badda Link road", "val6" => "1000", "val7" => "Delivered", "val8" => "Received"),
//         array("val0" => "019", "product_id" => 2, "qty" => 1, "val1" => "Apple Mahmud", "val2" => "01726014285", "val3" => "27/11/2024", "val4" => "coral pink mug x 1 ", "val5" => "Shagufta tower, Curetechbd,  Gulshan Badda Link road", "val6" => "300", "val7" => "Delivered", "val8" => "Received"),
//         array("val0" => "020", "product_id" => 5, "qty" => 2, "val1" => "Sayid mirza", "val2" => "01711555645", "val3" => "27/11/2024", "val4" => "Sweat jumper x 2, size M and L", "val5" => "Shagufta tower, Curetechbd,  Gulshan Badda Link road", "val6" => "1000", "val7" => "Delivered", "val8" => "Received"),
//         array("val0" => "021", "product_id" => 5, "qty" => 1, "val1" => "Risad Mahmud", "val2" => "01941196277", "val3" => "27/11/2024", "val4" => "Sweater jumper, size M", "val5" => "Tower Hamlet, 16 Kemal Ataturk Avenue, Banani, dhaka-1213( Al-Arafah islami bank, banani branch, 2nd floor)", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "022", "product_id" => 5, "qty" => 1, "val1" => "Alsarafat Ullah", "val2" => "01837106084", "val3" => "27/11/2024", "val4" => "Sweater jumper, size M", "val5" => "Hajiganj Rajargaon bazar", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "023", "product_id" => 5, "qty" => 1, "val1" => "Sagor", "val2" => "01321794437", "val3" => "27/11/2024", "val4" => "Sweater jumper, size M", "val5" => "Faridpur sadarpur", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "024", "product_id" => 5, "qty" => 1, "val1" => "Al Amin Ahsan", "val2" => "01614357236", "val3" => "28/11/2024", "val4" => "sweat jumper X1 S", "val5" => "Rajshahi, Chapai Nawabganj, Nachol, Nizampur Islami Bank(ground floor)", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "025", "product_id" => 3, "qty" => 1, "val1" => "RS Al-amin", "val2" => "01724121069", "val3" => "28/11/2024", "val4" => "realme 12 X1(i am a proud muslim)", "val5" => "Botbazar gacha road", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "026", "product_id" => 5, "qty" => 1, "val1" => "Alomgir Patowary", "val2" => "01862020259", "val3" => "28/11/2024", "val4" => "Sweater jumper, size XL", "val5" => "Ambagan, Matirangga, Khagrachari", "val6" => "500", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "027", "product_id" => 5, "qty" => 1, "val1" => "Ashraf ", "val2" => "01675787111", "val3" => "28/11/2024", "val4" => "sweat jumper X1 M (44 chest)", "val5" => "61 sikkatuli lean nazira bazar bangshal Dhaka near sikkatuli park ", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "028", "product_id" => 5, "qty" => 1, "val1" => "Mohammad Mehedi Hasan", "val2" => "01614265425", "val3" => "29/11/2024", "val4" => "sweat jumper X1 L", "val5" => "Curetech Office", "val6" => "500", "val7" => "Delivered", "val8" => "customer will pick up"),
//         array("val0" => "029", "product_id" => 5, "qty" => 1, "val1" => "Hm Habibullah Redoan ", "val2" => "01742900864 ", "val3" => "29/11/2024", "val4" => "sweat jumper X1 L", "val5" => "Dhaka centonment Balu ghat.  Baron tek", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "030", "product_id" => 5, "qty" => 1, "val1" => "Siam ", "val2" => "01325238314", "val3" => "30/11/2024", "val4" => "sweat jumper X1  Size M", "val5" => "kalihati, Tangail", "val6" => "500", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "031", "product_id" => 5, "qty" => 1, "val1" => "Md. Durul Huda", "val2" => "01871495034", "val3" => "30/11/2024", "val4" => "Sweat JumperX1 L", "val5" => "teacher ferdous Alam Firoj High School, Firoj Nagar Gandina, Kalihati, Tangail", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "032", "product_id" => 5, "qty" => 1, "val1" => "Faruk", "val2" => "01829089472", "val3" => "30/11/2024", "val4" => "Sweat JumperX1 L", "val5" => "D/s:chittagong, Thana: mirshari, Village:mithanala, Union: 10 number bot office ", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "033", "product_id" => 5, "qty" => 1, "val1" => "তারেক মাহমুদ", "val2" => "01646376749", "val3" => "01/12/2024", "val4" => "Sweat JumperX1 L", "val5" => "গ্রাম গাজির কান্দি ইউনিয়ন কৃষ্ণ নগর থানা নবীনগর জেলা ব্রাক্ষণবাড়িয়া ", "val6" => "600", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "034", "product_id" => 3, "qty" => 1, "val1" => "Salim Hosen", "val2" => "01762473884", "val3" => "01/12/2024", "val4" => "iphone 11 x1 (proud muslim)", "val5" => "House- TA136, (1st Floor, Sagufta Tower, Gulshan Badda Link Rd, Dhaka 1212", "val6" => "200", "val7" => "Customer Pickup", "val8" => ""),
//         array("val0" => "035", "product_id" => 3, "qty" => 1, "val1" => "মো: মাহমুদুল হাসান সা'দ", "val2" => "01991674949", "val3" => "01/12/2024", "val4" => "Samsung Galaxy A35 x1 (Caligraphy)", "val5" => "গ্রাম :নওদাপাড়া হাফিজিয়া মাদ্রাসা, আটঘরিয়া, পাবনা।", "val6" => "380", "val7" => "Pickup", "val8" => "Dec 4/5 tarik Delivery niben"),
//         array("val0" => "036", "product_id" => 3, "qty" => 1, "val1" => "Faria Islam", "val2" => "01711815400", "val3" => "01/12/2024", "val4" => "iPhone 11 x 1 (Caligraphy)", "val5" => "CureTech office 
//     Shagufta tower, Gulshan Badda link road", "val6" => "200", "val7" => "Customer Pickup", "val8" => ""),
//         array("val0" => "037", "product_id" => 3, "qty" => 1, "val1" => "Falguni Ahmmad Kaira", "val2" => "01757471130", "val3" => "01/12/2024", "val4" => "Samsung M14 x 1 (proud muslim)", "val5" => "Sagufta Tower, House- TA136, (1st Floor), Gulshan Badda Link Road, Dhaka 1212", "val6" => "200", "val7" => "Customer Pickup", "val8" => ""),
//         array("val0" => "038", "product_id" => 5, "qty" => 1, "val1" => "Md Muaj", "val2" => " 01971751799", "val3" => "01/12/2024", "val4" => "Sweat JumperX1 M", "val5" => "Chormonay, Barishal", "val6" => "550", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "039", "product_id" => 5, "qty" => 1, "val1" => "Rouzatul Jannat", "val2" => "01791913417", "val3" => "03/12/24", "val4" => "Sweat Jumper XL", "val5" => "Road 20, House 33, Rupnagar R/A, Mirpur, Dhaka 1216", "val6" => "500", "val7" => "Pickup", "val8" => ""),
//         array("val0" => "040", "product_id" => 3, "qty" => 1, "val1" => "Mariya ", "val2" => "01703933945", "val3" => "4/12/2024", "val4" => "Vivo y11 (I'm a proud Muslim)", "val5" => "Titas Cumilla , Batakandi Bajar Subuj Banglar samne", "val6" => "380", "val7" => "Pickup", "val8" => ""),
//     );


//     \DB::beginTransaction();
//     try {
//         foreach ($array as $arr) {

//             $sn = $arr['val0'];
//             $name = $arr['val1'];
//             $phone = $arr['val2'];
//             $date = $arr['val3'];
//             $product = $arr['val4'];
//             $addr = $arr['val5'];
//             $price = $arr['val6'];
//             $status = $arr['val7'];
//             $note = $arr['val8'];
//             $product_id = $arr['product_id'];
//             $qty = $arr['qty'];

//             $address = \App\Models\Address::where('phone', $phone)->first();

//             if (!$address) {
//                 $address = new Address();
//                 $address->address = $addr;
//                 $address->name = $name;
//                 $address->phone = $phone;
//                 $address->set_default = 1;
//                 $address->save();
//             }

//             $shippingAddress = [];
//             $shippingAddress['name']        = $address->name;
//             $shippingAddress['address']     = $address->address;
//             $shippingAddress['phone']       = $phone;



//             $order = new \App\Models\Order();
//             $order->ordered_from = "web";
//             $order->tracking_code = rand(1000, 9999);
//             $order->shipping_address = json_encode($shippingAddress);
//             $order->shipping_type = "delivery";
//             $order->payment_type = "cash_on_delivery";
//             $order->date = strtotime('now');
//             $order->save();

//             $product = \App\Models\Product::find($product_id);

//             $order_detail = new \App\Models\OrderDetail();
//             $order_detail->order_id = $order->id;
//             $order_detail->seller_id = 1;
//             $order_detail->product_id = $product_id;

//             $order_detail->price = $price * $qty;
//             $order_detail->tax = 0;

//             $order_detail->quantity = $qty;
//             $order_detail->save();

//             $grand_total = $price * $qty;

//             $order->grand_total = $grand_total;
//             $order->shipping_cost = 0;

//             $order_code = "100001";
//             $order_code_len = strlen($order_code);
//             $order_id_len = strlen("$order->id");
//             $new_order_code = $order->id;

//             if ($order_id_len < $order_code_len) {
//                 $order_code = substr($order_code, 0, $order_code_len - $order_id_len);
//                 $new_order_code = $order_code . $order->id;
//             }
//             $order->code = $new_order_code;
//             $order->save();

//         }
//         \DB::commit();
//         echo "DONE";
//     } catch (\Exception $e) {
//         \DB::rollBack();
//         throw $e;
//     }
// });

Route::post('sslcommerz-status', function(Request $request){

    $status = $request->order_status;
    $order_id = $request->order_id;
    if($status == "success"){
        $order = Order::find($order_id);
        $order->payment_status = "paid";
        $order->save();
    }

    return redirect("/purchased/$order_id");
})->name('sslcommerz.status');

Route::prefix('admin')->group(function () {

    // Route::resource('delivery-boys', DeliveryBoyController::class);



    Route::get("/hero", [PublicController::class, 'heroPrivacyPolicy']);
    Route::get("/", [LoginController::class, 'showLoginForm'])->middleware("guest");

    Auth::routes();

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [AdminController::class, 'admin_dashboard'])->name('admin.dashboard')->middleware(['auth', 'admin']);


    Route::group(['middleware' => ['auth', 'admin']], function () {

        Route::post('send-push-notification', [PushNotificationController::class, 'sendPushNotification'])->name("send.push.notification");


        Route::get("admin/getcdprice", function () {

            $externalURL = request("clink");
            $client = new \Goutte\Client();

            $website = $client->request('GET', $externalURL);

            $website->filter('.discountedPrice')->each(function ($node) {
                if ($node->matches('.discountedPrice')) {
                    echo $node->text();
                }
            });

            $website->filter('.price')->each(function ($node) {
                if ($node->matches('.price')) {
                    echo $node->text();
                }
            });

            return "";
        })->name("getcdprice");



        Route::get('/logout', [LoginController::class, 'logout']);

        Route::get('/products/price-scrapper', [ProductController::class, 'priceScrapper'])->name('price.scrapper');
        Route::post('/products/price-scrapper', [ProductController::class, 'updatePriceScrapper'])->name('price.scrapper');

        Route::get('/products/price-scrapper2', [ProductController::class, 'priceScrapper2'])->name('price.scrapper2');
        Route::post('/products/price-scrapper2', [ProductController::class, 'updatePriceScrapper2'])->name('price.scrapper2');

        Route::post('/products/store/', [ProductController::class, 'store'])->name('products.store');
        Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::get('/products/destroy/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/duplicate/{id}', [ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::post('/products/add-more-choice-option', [ProductController::class, 'add_more_choice_option'])->name('products.add-more-choice-option');
        Route::post('/products/sku_combination', [ProductController::class, 'sku_combination'])->name('products.sku_combination');
        Route::post('/products/sku_combination_edit', [ProductController::class, 'sku_combination_edit'])->name('products.sku_combination_edit');
        Route::post('/products/seller/featured', [ProductController::class, 'updateSellerFeatured'])->name('products.seller.featured');
        Route::post('/products/published', [ProductController::class, 'updatePublished'])->name('products.published');

        Route::get('invoice/{order_id}', [InvoiceController::class, 'invoice_download'])->name('invoice.download');
        Route::post('/orders/delivery-boy-assign', [OrderController::class, 'assign_delivery_boy'])->name('orders.delivery-boy-assign');
        Route::post('/orders/update_delivery_status', [OrderController::class, 'update_delivery_status'])->name('orders.update_delivery_status');
        Route::post('/orders/update_payment_status', [OrderController::class, 'update_payment_status'])->name('orders.update_payment_status');

        //Update Routes

        Route::resource("vendors", VendorController::class);
        Route::get("/vendor/products/{id}", [VendorController::class, "products"])->name("vendor.products");
        Route::post("/vendor/products/add", [VendorController::class, "addVendorProduct"])->name("vendor.products.add");
        Route::get("/vendor-products/delete/{id}", [VendorController::class, "deleteVendorProduct"])->name("vendor.products.delete");

        Route::get("/stock-in", [VendorController::class, "stockInHistory"])->name("stockin.history");
        Route::get("/stockin/create", [VendorController::class, "createStockIn"])->name("stockin.create");
        Route::post("/stockin/store", [VendorController::class, "storeStockIn"])->name("stockin.store");
        Route::get("/stockin/destory/{id}", [VendorController::class, "deleteStockIn"])->name("stockin.destroy");


        // Stock Out
        Route::get("/stock-out", [VendorController::class, "stockOutHistory"])->name("stockout.history");
        Route::get("/stockout/create", [VendorController::class, "createStockOut"])->name("stockout.create");
        Route::post("/stockout/store", [VendorController::class, "storeStockOut"])->name("stockout.store");
        Route::get("/stockout/destory/{id}", [VendorController::class, "deleteStockOut"])->name("stockout.destroy");


        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
        Route::get('/categories/edit/{id}', [\App\Http\Controllers\CategoryController::class, 'edit'])->name('categories.edit');
        Route::get('/categories/destroy/{id}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/featured', [\App\Http\Controllers\CategoryController::class, 'updateFeatured'])->name('categories.featured');

        Route::resource('brands', BrandController::class);
        Route::get('/brands/edit/{id}', [BrandControlle::class, 'edit'])->name('brands.edit');
        Route::get('/brands/destroy/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');

        Route::get('/products/admin', [ProductController::class, 'admin_products'])->name('products.admin');
        Route::get('/products/seller', [ProductController::class, 'seller_products'])->name('products.seller');
        Route::get('/products/all', [ProductController::class, 'all_products'])->name('products.all');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('/products/admin/{id}/edit', [ProductController::class, 'admin_product_edit'])->name('products.admin.edit');
        Route::get('/products/seller/{id}/edit', [ProductController::class, 'seller_product_edit'])->name('products.seller.edit');
        Route::post('/products/todays_deal', [ProductController::class, 'updateTodaysDeal'])->name('products.todays_deal');
        Route::post('/products/featured', [ProductController::class, 'updateFeatured'])->name('products.featured');
        Route::post('/products/approved', [ProductController::class, 'updateProductApproval'])->name('products.approved');
        Route::post('/products/get_products_by_subcategory', [ProductController::class, 'get_products_by_subcategory'])->name('products.get_products_by_subcategory');
        Route::post('/bulk-product-delete', [ProductController::class, 'bulk_product_delete'])->name('bulk-product-delete');


        Route::resource('sellers', SellerControlle::class);
        Route::get('sellers_ban/{id}', [SellerController::class, 'ban'])->name('sellers.ban');
        Route::get('/sellers/destroy/{id}', [SellerController::class, 'destroy'])->name('sellers.destroy');
        Route::post('/bulk-seller-delete', [SellerController::class, 'bulk_seller_delete'])->name('bulk-seller-delete');
        Route::get('/sellers/view/{id}/verification', [SellerController::class, 'show_verification_request'])->name('sellers.show_verification_request');
        Route::get('/sellers/approve/{id}', [SellerController::class, 'approve_seller'])->name('sellers.approve');
        Route::get('/sellers/reject/{id}', [SellerController::class, 'reject_seller'])->name('sellers.reject');
        Route::get('/sellers/login/{id}', [SellerController::class, 'login'])->name('sellers.login');
        Route::post('/sellers/payment_modal', [SellerController::class, 'payment_modal'])->name('sellers.payment_modal');
        Route::get('/seller/payments', [PaymentController::class, 'payment_histories'])->name('sellers.payment_histories');
        Route::get('/seller/payments/show/{id}', [PaymentController::class, 'show'])->name('sellers.payment_history');

        Route::resource('customers', CustomerController::class);
        Route::get('b2b-customers', [CustomerController::class, 'b2bCustomers'])->name("customers.b2b");
        Route::get('b2b-customer-request', [CustomerController::class, 'b2bRequest'])->name("customers.b2b.request");
        Route::get('customers_ban/{customer}', [CustomerController::class, 'ban'])->name('customers.ban');
        Route::get('/customers/login/{id}', [CustomerController::class, 'login'])->name('customers.login');
        Route::get('/customers/destroy/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('/bulk-customer-delete', [CustomerController::class, 'bulk_customer_delete'])->name('bulk-customer-delete');

        //Customer List Export
        Route::get('customer/export', [CustomerController::class, 'customerExport'])->name('customer.export');


        Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletters.index');
        Route::post('/newsletter/send', [NewsletterController::class, 'send'])->name('newsletters.send');
        Route::post('/newsletter/test/smtp', [NewsletterController::class, 'testEmail'])->name('test.smtp');

        Route::resource('profile', ProfileController::class);

        Route::post('/business-settings/update', [BusinessSettingsController::class, 'update'])->name('business_settings.update');
        Route::post('/business-settings/update/activation', [BusinessSettingsController::class, 'updateActivationSettings'])->name('business_settings.update.activation');
        Route::get('/general-setting', [BusinessSettingsController::class, 'general_setting'])->name('general_setting.index');
        Route::get('/activation', [BusinessSettingsController::class, 'activation'])->name('activation.index');
        Route::get('/payment-method', [BusinessSettingsController::class, 'payment_method'])->name('payment_method.index');
        Route::get('/file_system', [BusinessSettingsController::class, 'file_system'])->name('file_system.index');
        Route::get('/social-login', [BusinessSettingsController::class, 'social_login'])->name('social_login.index');
        Route::get('/smtp-settings', [BusinessSettingsController::class, 'smtp_settings'])->name('smtp_settings.index');
        Route::get('/google-analytics', [BusinessSettingsController::class, 'google_analytics'])->name('google_analytics.index');
        Route::get('/google-recaptcha', [BusinessSettingsController::class, 'google_recaptcha'])->name('google_recaptcha.index');
        Route::get('/google-map', [BusinessSettingsController::class, 'google_map'])->name('google-map.index');
        Route::get('/google-firebase', [BusinessSettingsController::class, 'google_firebase'])->name('google-firebase.index');

        //Facebook Settings
        Route::get('/facebook-chat', [BusinessSettingsController::class, 'facebook_chat'])->name('facebook_chat.index');
        Route::post('/facebook_chat', [BusinessSettingsController::class, 'facebook_chat_update'])->name('facebook_chat.update');
        Route::get('/facebook-comment', [BusinessSettingsController::class, 'facebook_comment'])->name('facebook-comment');
        Route::post('/facebook-comment', [BusinessSettingsController::class, 'facebook_comment_update'])->name('facebook-comment.update');
        Route::post('/facebook_pixel', [BusinessSettingsController::class, 'facebook_pixel_update'])->name('facebook_pixel.update');

        Route::post('/env_key_update', [BusinessSettingsController::class, 'env_key_update'])->name('env_key_update.update');
        Route::post('/payment_method_update', [BusinessSettingsController::class, 'payment_method_update'])->name('payment_method.update');
        Route::post('/google_analytics', [BusinessSettingsController::class, 'google_analytics_update'])->name('google_analytics.update');
        Route::post('/google_recaptcha', [BusinessSettingsController::class, 'google_recaptcha_update'])->name('google_recaptcha.update');
        Route::post('/google-map', [BusinessSettingsController::class, 'google_map_update'])->name('google-map.update');
        Route::post('/google-firebase', [BusinessSettingsController::class, 'google_firebase_update'])->name('google-firebase.update');
        //Currency
        Route::get('/currency', [CurrencyController::class, 'currency'])->name('currency.index');
        Route::post('/currency/update', [CurrencyController::class, 'updateCurrency'])->name('currency.update');
        Route::post('/your-currency/update', [CurrencyController::class, 'updateYourCurrency'])->name('your_currency.update');
        Route::get('/currency/create', [CurrencyController::class, 'create'])->name('currency.create');
        Route::post('/currency/store', [CurrencyController::class, 'store'])->name('currency.store');
        Route::post('/currency/currency_edit', [CurrencyController::class, 'edit'])->name('currency.edit');
        Route::post('/currency/update_status', [CurrencyController::class, 'update_status'])->name('currency.update_status');

        //Tax
        Route::resource('tax', TaxController::class);
        Route::get('/tax/edit/{id}', [TaxController::class, 'edit'])->name('tax.edit');
        Route::get('/tax/destroy/{id}', [TaxController::class, 'destroy'])->name('tax.destroy');
        Route::post('tax-status', [TaxController::class, 'change_tax_status'])->name('taxes.tax-status');


        Route::get('/verification/form', [BusinessSettingsController::class, 'seller_verification_form'])->name('seller_verification_form.index');
        Route::post('/verification/form', [BusinessSettingsController::class, 'seller_verification_form_update'])->name('seller_verification_form.update');
        Route::get('/vendor_commission', [BusinessSettingsController::class, 'vendor_commission'])->name('business_settings.vendor_commission');
        Route::post('/vendor_commission_update', [BusinessSettingsController::class, 'vendor_commission_update'])->name('business_settings.vendor_commission.update');

        Route::resource('/languages', LanguageController::class);
        Route::post('/languages/{id}/update', [LanguageController::class, 'update'])->name('languages.update');
        Route::get('/languages/destroy/{id}', [LanguageController::class, 'destroy'])->name('languages.destroy');
        Route::post('/languages/update_rtl_status', [LanguageController::class, 'update_rtl_status'])->name('languages.update_rtl_status');
        Route::post('/languages/update-status', [LanguageController::class, 'update_status'])->name('languages.update-status');
        Route::post('/languages/key_value_store', [LanguageController::class, 'key_value_store'])->name('languages.key_value_store');

        //App Trasnlation
        Route::post('/languages/app-translations/import', [LanguageController::class, 'importEnglishFile'])->name('app-translations.import');
        Route::get('/languages/app-translations/show/{id}', [LanguageController::class, 'showAppTranlsationView'])->name('app-translations.show');
        Route::post('/languages/app-translations/key_value_store', [LanguageController::class, 'storeAppTranlsation'])->name('app-translations.store');
        Route::get('/languages/app-translations/export/{id}', [LanguageController::class, 'exportARBFile'])->name('app-translations.export');

        // website setting
        Route::group(['prefix' => 'website'], function () {
            Route::get('/footer', [WebsiteController::class, 'footer'])->name('website.footer');
            Route::get('/header', [WebsiteController::class, 'header'])->name('website.header');
            Route::get('/appearance', [WebsiteController::class, 'appearance'])->name('website.appearance');
            Route::get('/pages', [WebsiteController::class, 'pages'])->name('website.pages');
            Route::resource('custom-pages', PageController::class);
            Route::get('/custom-pages/edit/{id}', [PageController::class, 'edit'])->name('custom-pages.edit');
            Route::get('/custom-pages/destroy/{id}', [PageController::class, 'destroy'])->name('custom-pages.destroy');
        });

        Route::resource('roles', RoleController::class);
        Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::get('/roles/destroy/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::resource('staffs', StaffController::class);
        Route::get('/staffs/destroy/{id}', [StaffController::class, 'destroy'])->name('staffs.destroy');

        Route::resource('flash_deals', FlashDealController::class);
        Route::get('/flash_deals/edit/{id}', [FlashDealController::class, 'edit'])->name('flash_deals.edit');
        Route::get('/flash_deals/destroy/{id}', [FlashDealController::class, 'destroy'])->name('flash_deals.destroy');
        Route::post('/flash_deals/update_status', [FlashDealController::class, 'update_status'])->name('flash_deals.update_status');
        Route::post('/flash_deals/update_featured', [FlashDealController::class, 'update_featured'])->name('flash_deals.update_featured');
        Route::post('/flash_deals/product_discount', [FlashDealController::class, 'product_discount'])->name('flash_deals.product_discount');
        Route::post('/flash_deals/product_discount_edit', [FlashDealController::class, 'product_discount_edit'])->name('flash_deals.product_discount_edit');

        //Subscribers
        Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
        Route::get('/subscribers/destroy/{id}', [SubscriberController::class, 'destroy'])->name('subscriber.destroy');
        Route::get('/subscription/configuration', [SubscriberController::class, 'subscription_configuration'])->name('subscription.configuration');


        // All Orders
        Route::get('/new-sale', [OrderController::class, 'newSale'])->name('newsale');
        Route::post('/new-sale', [OrderController::class, 'storeNewSale'])->name('newsale.store');
        Route::get('/b2b-orders', [OrderController::class, 'b2bOrders'])->name('b2b_orders.index');
        Route::post('/new-sale/{id}', [OrderController::class, 'newsalePayment'])->name('newsale.payment');

        Route::post("give-discount/{order_id}", [OrderController::class, "giveDiscount"])->name("discount.give");
        Route::get('/all_orders', [OrderController::class, 'all_orders'])->name('all_orders.index');
        Route::post('/all_orders/{id}/cancel', [OrderController::class, 'all_orders_cancel'])->name('all_orders.cancel');
        Route::get('/all_orders/{id}/show', [OrderController::class, 'all_orders_show'])->name('all_orders.show');
        Route::post('/all_orders/product/add', [OrderController::class, 'add_product'])->name('customer.add_product');
        Route::get('/order/details/destroy/{id}', [OrderController::class, 'order_destroy'])->name('destroy.orderdetails');
        Route::post('/orders/manageby', [OrderController::class, 'manageBy'])->name('orders.manageby');
        Route::post("/bkash/refund", [BkashController::class, "refund"])->name("bkash.refund");
        Route::post("/bkash/refund-details", [BkashController::class, "refundDetails"])->name("bkash.refund.details");

        // Inhouse Orders
        Route::get('/inhouse-orders', [OrderController::class, 'admin_orders'])->name('inhouse_orders.index');
        Route::get('/inhouse-orders/{id}/show', [OrderController::class, 'show'])->name('inhouse_orders.show');

        // Seller Orders
        Route::get('/seller_orders', [OrderController::class, 'seller_orders'])->name('seller_orders.index');
        Route::get('/seller_orders/{id}/show', [OrderController::class, 'seller_orders_show'])->name('seller_orders.show');

        Route::post('/bulk-order-status', [OrderController::class, 'bulk_order_status'])->name('bulk-order-status');


        // Pickup point orders
        Route::get('orders_by_pickup_point', [OrderController::class, 'pickup_point_order_index'])->name('pick_up_point.order_index');
        Route::get('/orders_by_pickup_point/{id}/show', [OrderController::class, 'pickup_point_order_sales_show'])->name('pick_up_point.order_show');

        Route::get('/orders/destroy/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/bulk-order-delete', [OrderController::class, 'bulk_order_delete'])->name('bulk-order-delete');

        Route::post('/pay_to_seller', [CommissionController::class, 'pay_to_seller'])->name('commissions.pay_to_seller');

        //Skywalk Order Export
        Route::get('skywalk/order/export', [OrderController::class, 'export'])->name('skywalk_order_export.index');

        //Reports
        Route::get('/stock_report', [ReportController::class, 'stock_report'])->name('stock_report.index');
        Route::get('/in_house_sale_report', [ReportController::class, 'in_house_sale_report'])->name('in_house_sale_report.index');
        Route::get('/seller_sale_report', [ReportController::class, 'seller_sale_report'])->name('seller_sale_report.index');
        Route::get('/wish_report', [ReportController::class, 'wish_report'])->name('wish_report.index');
        Route::get('/user_search_report', [ReportController::class, 'user_search_report'])->name('user_search_report.index');
        Route::get('/wallet-history', [ReportController::class, 'wallet_transaction_history'])->name('wallet-history.index');

        //Blog Section
        Route::resource('blog-category', BlogCategoryController::class);
        Route::get('/blog-category/destroy/{id}', [BlogCategoryController::class, 'destroy'])->name('blog-category.destroy');
        Route::resource('blog', BlogController::class);
        Route::get('/blog/destroy/{id}', [BlogController::class, 'destroy'])->name('blog.destroy');
        Route::post('/blog/change-status', [BlogController::class, 'change_status'])->name('blog.change-status');

        //Coupons
        Route::resource('coupon', CouponController::class);
        Route::get('/coupon/destroy/{id}', [CouponController::class, 'destroy'])->name('coupon.destroy');
        // //Coupon Form
        Route::post('/coupon/get_form', [CouponController::class, 'get_coupon_form'])->name('coupon.get_coupon_form');
        Route::post('/coupon/get_form_edit', [CouponController::class, 'get_coupon_form_edit'])->name('coupon.get_coupon_form_edit');


        //Push Notification
        Route::resource('push-notification', PushNotificationController::class);

        //Price History
        Route::resource('price-histories', PriceHistoryController::class);

        //Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/published', [ReviewController::class, 'updatePublished'])->name('reviews.published');

        //Support_Ticket
        Route::get('support_ticket/', [SupportTicketController::class, 'admin_index'])->name('support_ticket.admin_index');
        Route::get('support_ticket/{id}/show', [SupportTicketController::class, 'admin_show'])->name('support_ticket.admin_show');
        Route::post('support_ticket/reply', [SupportTicketController::class, 'admin_store'])->name('support_ticket.admin_store');

        //Pickup_Points
        Route::resource('pick_up_points', PickupPointController::class);
        Route::get('/pick_up_points/edit/{id}', [PickupPointController::class, 'edit'])->name('pick_up_points.edit');
        Route::get('/pick_up_points/destroy/{id}', [PickupPointController::class, 'destroy'])->name('pick_up_points.destroy');

        //conversation of seller customer
        Route::get('conversations', [ConversationController::class, 'admin_index'])->name('conversations.admin_index');
        Route::get('conversations/{id}/show', [ConversationController::class, 'admin_show'])->name('conversations.admin_show');

        Route::post('/sellers/profile_modal', [SellerController::class, 'profile_modal'])->name('sellers.profile_modal');
        Route::post('/sellers/approved', [SellerController::class, 'updateApproved'])->name('sellers.approved');

        Route::resource('attributes', AttributeController::class);
        Route::get('/attributes/edit/{id}', [AttributeController::class, 'edit'])->name('attributes.edit');
        Route::get('/attributes/destroy/{id}', [AttributeController::class, 'destroy'])->name('attributes.destroy');

        //Attribute Value
        Route::post('/store-attribute-value', [AttributeController::class, 'store_attribute_value'])->name('store-attribute-value');
        Route::get('/edit-attribute-value/{id}', [AttributeController::class, 'edit_attribute_value'])->name('edit-attribute-value');
        Route::post('/update-attribute-value/{id}', [AttributeController::class, 'update_attribute_value'])->name('update-attribute-value');
        Route::get('/destroy-attribute-value/{id}', [AttributeController::class, 'destroy_attribute_value'])->name('destroy-attribute-value');

        //Colors
        Route::get('/colors', [AttributeController::class, 'colors'])->name('colors');
        Route::post('/colors/store', [AttributeController::class, 'store_color'])->name('colors.store');
        Route::get('/colors/edit/{id}', [AttributeController::class, 'edit_color'])->name('colors.edit');
        Route::post('/colors/update/{id}', [AttributeController::class, 'update_color'])->name('colors.update');
        Route::get('/colors/destroy/{id}', [AttributeController::class, 'destroy_color'])->name('colors.destroy');

        Route::resource('addons', AddonController::class);
        Route::post('/addons/activation', [AddonController::class, 'activation'])->name('addons.activation');

        Route::get('/customer-bulk-upload/index', [CustomerBulkUploadController::class, 'index'])->name('customer_bulk_upload.index');
        Route::post('/bulk-user-upload', [CustomerBulkUploadController::class, 'user_bulk_upload'])->name('bulk_user_upload');
        Route::post('/bulk-customer-upload', [CustomerBulkUploadController::class, 'customer_bulk_file'])->name('bulk_customer_upload');
        Route::get('/user', [CustomerBulkUploadController::class, 'pdf_download_user'])->name('pdf.download_user');


        //Customer Package
        Route::resource('customer_packages', CustomerPackageController::class);
        Route::get('/customer_packages/edit/{id}', [CustomerPackageController::class, 'edit'])->name('customer_packages.edit');
        Route::get('/customer_packages/destroy/{id}', [CustomerPackageController::class, 'destroy'])->name('customer_packages.destroy');

        //Classified Products
        Route::get('/classified_products', [CustomerProductController::class, 'customer_product_index'])->name('classified_products');
        Route::post('/classified_products/published', [CustomerProductController::class, 'updatePublished'])->name('classified_products.published');

        //Shipping Configuration
        Route::get('/shipping_configuration', [BusinessSettingsController::class, 'shipping_configuration'])->name('shipping_configuration.index');
        Route::post('/shipping_configuration/update', [BusinessSettingsController::class, 'shipping_configuration_update'])->name('shipping_configuration.update');

        // Route::resource('pages', 'PageController');
        // Route::get('/pages/destroy/{id}', 'PageController@destroy')->name('pages.destroy');

        Route::resource('countries', CountryController::class);
        Route::post('/countries/status', [CountryController::class, 'updateStatus'])->name('countries.status');

        Route::resource('states', StateController::class);
        Route::post('/states/status', [StateController::class, 'updateStatus'])->name('states.status');

        Route::resource('cities', CityController::class);
        Route::get('/cities/edit/{id}', [CityController::class, 'edit'])->name('cities.edit');
        Route::get('/cities/destroy/{id}', [CityController::class, 'destroy'])->name('cities.destroy');
        Route::post('/cities/status', [CityController::class, 'updateStatus'])->name('cities.status');

        Route::view('/system/update', 'backend.system.update')->name('system_update');
        Route::view('/system/server-status', 'backend.system.server_status')->name('system_server');

        // uploaded files
        Route::any('/uploaded-files/file-info', [AizUploadController::class, 'file_info'])->name('uploaded-files.info');
        Route::resource('/uploaded-files', AizUploadController::class);
        Route::get('/uploaded-files/destroy/{id}', [AizUploadController::class, 'destroy'])->name('uploaded-files.destroy');

        Route::post('/aiz-uploader', [AizUploadController::class, 'show_uploader']);
        Route::post('/aiz-uploader/upload', [AizUploadController::class, 'upload']);
        Route::get('/aiz-uploader/get_uploaded_files', [AizUploadController::class, 'get_uploaded_files']);
        Route::post('/aiz-uploader/get_file_by_ids', [AizUploadController::class, 'get_preview_files']);
        Route::get('/aiz-uploader/download/{id}', [AizUploadController::class, 'attachment_download'])->name('download_attachment');

        Route::get('/all-notification', [NotificationController::class, 'index'])->name('admin.all-notification');

        Route::get('/cache-cache', [AdminController::class, 'clearCache'])->name('cache.clear');

        //Cart Analysis
        Route::get('cart/analysis', [CartAnalysisController::class, 'index'])->name('cart.index');

        //Customer Feedback
        Route::resource('feedback', CustomerFeedbackController::class);
        Route::get('feeedback/export', [CustomerFeedbackController::class, 'export'])->name('feedback.export');
        Route::post('customer/feedback/import', [CustomerFeedbackController::class, 'import'])->name('feedback.import');

        //Customer Complain
        Route::resource('complain', CustomerComplainController::class);

        //Customer Cancel List
        Route::get('customer/cancel/list', [CustomerCancelListController::class, 'index'])->name('cancel_list.index');
        Route::get('customer/cancel/list/create', [CustomerCancelListController::class, 'create'])->name('cancel_list.create');
        Route::post('customer/cancel/list/store', [CustomerCancelListController::class, 'store'])->name('cancel_list.store');
        Route::get('customer/cancel/list/edit/{id}', [CustomerCancelListController::class, 'edit'])->name('cancel_list.edit');
        Route::post('customer/cancel/list/update/{id}', [CustomerCancelListController::class, 'update'])->name('cancel_list.update');
        Route::get('customer/cancel/list/destroy/{id}', [CustomerCancelListController::class, 'destroy'])->name('cancel_list.destroy');
        Route::get('customer/cancel/list/export', [CustomerCancelListController::class, 'export'])->name('cancel_list.export');
        Route::get('customer/complain/export', [CustomerComplainController::class, 'export'])->name('complain.export');
        Route::post('customer/complain/import', [CustomerComplainController::class, 'import'])->name('complain.import');

        //Customer mergency Balance
        Route::get('emergency/balance', [CustomerController::class, 'emergencyBalance'])->name('emergency.balance');


        //user Activity History
        Route::resource('user-log', UserActivityHistoryController::class);

        Route::get('/product/{slug}', [HomeController::class, 'product'])->name('product');

        //Product Bulk Upload
        Route::get('/product-bulk-upload/index', [ProductBulkUploadController::class, 'index'])->name('product_bulk_upload.index');
        Route::post('/bulk-product-upload', [ProductBulkUploadController::class, 'bulk_upload'])->name('bulk_product_upload');
        Route::get('/product-csv-download/{type}', [ProductBulkUploadController::class, 'import_product'])->name('product_csv.download');
        Route::get('/vendor-product-csv-download/{id}', [ProductBulkUploadController::class, 'import_vendor_product'])->name('import_vendor_product.download');
        Route::group(['prefix' => 'bulk-upload/download'], function () {
            Route::get('/category', [ProductBulkUploadController::class, 'pdf_download_category'])->name('pdf.download_category');
            Route::get('/brand', [ProductBulkUploadController::class, 'pdf_download_brand'])->name('pdf.download_brand');
            Route::get('/seller', [ProductBulkUploadController::class, 'pdf_download_seller'])->name('pdf.download_seller');
        });

        // //Product Export
        Route::get('/product-bulk-export', [ProductBulkUploadController::class, 'export'])->name('product_bulk_export.index');
        Route::post('/product-bulk-import', [ProductBulkUploadController::class, 'import'])->name('product_bulk_import.index');


        // //Reports
        Route::get('/commission-log', [ReportController::class, 'commission_history'])->name('commission-log.index');
        Route::post('/language', [LanguageController::class, 'changeLanguage'])->name('language.change');

        //Warehouse
        Route::resource('warehouse', WarehouseController::class);

        Route::resource('story-group', StoryGroupController::class);
        Route::resource('stories', StoryController::class);



        Route::resource('delivery-boys', DeliveryBoyController::class);
        Route::get('/delivery-boy-configuration', [DeliveryBoyController::class, 'delivery_boy_configure'])->name('delivery-boy-configuration');
        Route::get('/delivery-boys-payment-histories', [DeliveryBoyController::class, 'delivery_boys_payment_histories'])->name('delivery-boys-payment-histories');
        Route::get('/delivery-boys-collection-histories', [DeliveryBoyController::class, 'delivery_boys_collection_histories'])->name('delivery-boys-collection-histories');
        Route::get("rider-order-collections", [\App\Http\Controllers\RiderController::class, "riderOrderCollection"])->name("rider.order.collections");
        Route::get("rider-payment-history", [\App\Http\Controllers\RiderController::class, "riderPaymentHistory"])->name("rider.payment.history");
        Route::get("rider-payment-show/{id}", [\App\Http\Controllers\RiderController::class, "riderPaymentShow"])->name("rider.payment.show");
        Route::get("mark-rider-payment-paid/{id}", [\App\Http\Controllers\RiderController::class, "markRiderPaymentAsPaid"])->name("rider.payment.paid");

        Route::view("referral", "backend.marketing.referral")->name("referral");

        // //Custom page
        Route::get('/{slug}', [PageController::class, 'show_custom_page'])->name('custom-pages.show_custom_page');
    });
});



// Important things to do
// add update current_stock with product stock
// run /admin/stock-sync

// update address city_id column by state_id  ---
// update addresses set city_id = state_id;

// add columns to orders table
// run migration of orders table
// php artisan migrate --path="database/migrations/2022_10_18_160949_add_columns_to_orders_table.php"

// add city_id column to delivery_boys
// php artisan migrate --path="database/migrations/2022_10_18_160949_add_columns_to_delivery_boys_table.php"

// add sku column to products table;  ---
// alter table products add sku varchar(50) default null;

// add distance column to delivery_histories table ---
// ALTER TABLE delivery_histories DROP COLUMN delivery_status;
// ALTER TABLE delivery_histories DROP COLUMN payment_type;
// ALTER TABLE delivery_histories Add distance double(8,2) default 0;
// ALTER TABLE delivery_histories Add 	delivery_boy_payment_id bigint(12) null;

// change column of delivery_payments  ---
// ALTER TABLE delivery_boy_payments RENAME COLUMN user_id TO rider_id;
// ALTER TABLE delivery_boy_payments RENAME COLUMN payment TO amount;
// alter table delivery_boy_payments add status varchar(50) default 'pending';
// alter table delivery_boy_payments add reject_reason varchar(255) default null;

// add invisible earning column do delivery_boys table
// alter table delivery_boys add invisible_earning double(10,2) default 0; ---

// php artisan migrate --path="database/migrations/2022_10_18_160949_add_expires_at_to_personal_access_tokens_table.php"


// alter table delivery_boys add fcm_token varchar(255) default null; ---
// alter table orders drop column shipping_type;

// php artisan migrate --path="database/migrations/2022_11_02_114838_create_warehouses_table.php"
// php artisan migrate --path="database/migrations/2022_11_02_114839_create_city_warehouse_table.php"

// php artisan migrate --path="database/migrations/2022_11_08_183438_create_story_groups_table.php"
// php artisan migrate --path="database/migrations/2022_11_08_183459_create_stories_table.php"


// update tracking code in sms


// ALTER TABLE orders MODIFY COLUMN shipping_type varchar(100) default null;
// alter table delivery_boys add dob date default null;

//*** migrate all states to cities in app




// alter table users add column is_d tinyint(1) default 0;
// alter table orders add column is_d tinyint(1) default 0;

//  alter table products add column clink varchar(100) default null;
//  alter table products add column batch_id varchar(100) default null;

// dummy comment
// alter table categories add column image varchar(100) default null;

// alter table categories add column is_active tinyint(1) default 1;


// alter table delivery_boys add column nid_image_backpart varchar(100) default null;
// alter table delivery_boys add column license_image_backpart varchar(100) default null;

// alter table users add column is_new_user tinyint(1) default false;

// alter table users add column is_b2b_user tinyint(1) default null;

// alter table categories add column is_b2b_category tinyint(1) default false;

// alter table orders add column referral_code varchar(50) default null;


// pixel
// b2b no reward
// price less 2 from chaldal product


// alter table orders add column is_b2b_order tinyint(1) default false;
// alter table orders add COLUMN paid_amount double(10,2) DEFAULT 0;
// alter table orders add COLUMN due_amount double(10,2) DEFAULT 0;
// alter table orders add column created_by bigint;

// alter table cities add column latitude decimal(10,8) default null;
// alter table cities add column longitude decimal(10,8) default null;
// alter table orders add column invoice_number varchar(100) default null;

// alter table users add column lock_emergency tinyint(1) default 0;
// alter table orders add column is_emergency_order tinyint(1) default 0;
// alter table users add column delivery_subscription tinyint(1) default 0;


// ALTER TABLE `products` ADD `regular_price` DOUBLE(10,2) NULL DEFAULT NULL AFTER `unit_price`;
// ALTER TABLE `products` ADD `attribute_name` VARCHAR(100) NULL AFTER `name`;