<?php

/*
|--------------------------------------------------------------------------
| POS Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\DeliveryBoyController;
use App\Http\Controllers\RiderController;

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){

    //Delivery Boy

    Route::resource('delivery-boys', DeliveryBoyController::class);
    Route::get('/delivery-boy-configuration', [DeliveryBoyController::class, 'delivery_boy_configure'])->name('delivery-boy-configuration');
    Route::get('/delivery-boys-payment-histories', [DeliveryBoyController::class, 'delivery_boys_payment_histories'])->name('delivery-boys-payment-histories');
    Route::get('/delivery-boys-collection-histories', [DeliveryBoyController::class, 'delivery_boys_collection_histories'])->name('delivery-boys-collection-histories');
    Route::get("rider-order-collections", [RiderController::class, "riderOrderCollection"])->name("rider.order.collections");
    Route::get("rider-payment-history", [RiderController::class, "riderPaymentHistory"])->name("rider.payment.history");
    Route::get("rider-payment-show/{id}", [RiderController::class, "riderPaymentShow"])->name("rider.payment.show");
    Route::get("mark-rider-payment-paid/{id}", [RiderController::class, "markRiderPaymentAsPaid"])->name("rider.payment.paid");

    // Route::controller(DeliveryBoyController::class)->group(function () {
    //     Route::get('/delivery-boy/ban/{id}', 'ban')->name('delivery-boy.ban');
    //     Route::get('/delivery-boy-configuration', 'delivery_boy_configure')->name('delivery-boy-configuration');
    //     Route::post('/delivery-boy/order-collection', 'order_collection_form')->name('delivery-boy.order-collection');
    //     Route::post('/collection-from-delivery-boy', 'collection_from_delivery_boy')->name('collection-from-delivery-boy');
    //     Route::post('/delivery-boy/delivery-earning', 'delivery_earning_form')->name('delivery-boy.delivery-earning');
    //     Route::post('/paid-to-delivery-boy', 'paid_to_delivery_boy')->name('paid-to-delivery-boy');
    //     Route::get('/delivery-boys-payment-histories', 'delivery_boys_payment_histories')->name('delivery-boys-payment-histories');
    //     Route::get('/delivery-boys-collection-histories', 'delivery_boys_collection_histories')->name('delivery-boys-collection-histories');
    //     Route::get('/delivery-boy/cancel-request', 'cancel_request_list')->name('delivery-boy.cancel-request');
	//     Route::get("/delivery-boy/collections/{user_id}", "riderColections")->name("deliveryboys.collection");

    //     Route::get("rider-order-collections", [RiderController::class, "riderOrderCollection"])->name("rider.order.collections");
    //     Route::get("rider-payment-history", [RiderController::class, "riderPaymentHistory"])->name("rider.payment.history");
    //     Route::get("rider-payment-show/{id}", [RiderController::class, "riderPaymentShow"])->name("rider.payment.show");
    //     Route::get("mark-rider-payment-paid/{id}", [RiderController::class, "markRiderPaymentAsPaid"])->name("rider.payment.paid");
    // });
});

// Route::group(['middleware' => ['user', 'verified', 'unbanned']], function() {
//     Route::controller(DeliveryBoyController::class)->group(function () {
//         Route::get('/assigned-deliveries', 'assigned_delivery')->name('assigned-deliveries');
//         Route::get('/pickup-deliveries', 'pickup_delivery')->name('pickup-deliveries');
//         Route::get('/on-the-way-deliveries', 'on_the_way_deliveries')->name('on-the-way-deliveries');
//         Route::get('/completed-deliveries', 'completed_delivery')->name('completed-deliveries');
//         Route::get('/pending-deliveries', 'pending_delivery')->name('pending-deliveries');
//         Route::get('/cancelled-deliveries', 'cancelled_delivery')->name('cancelled-deliveries');
//         Route::get('/total-collections', 'total_collection')->name('total-collection');
//         Route::get('/total-earnings', 'total_earning')->name('total-earnings');
//         Route::get('/cancel-request/{id}', 'cancel_request')->name('cancel-request');
//         Route::get('/cancel-request-list', 'cancel_request_list')->name('cancel-request-list');
//     });
// });