<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\ClubPointController;
use App\Http\Resources\V3\OrderIndexResource;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\CommissionHistory;
use App\Models\Color;
use App\Models\OrderDetail;
use App\Models\CouponUsage;
use App\Models\Coupon;
use App\OtpConfiguration;
use App\Models\User;
use App\Models\BusinessSetting;
use App\Models\CombinedOrder;
use App\Models\SmsTemplate;
use Auth;
use Session;
use DB;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Models\ClubPoint;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyPayment;
use App\Models\DeliveryHistory;
use App\Utility\NotificationUtility;
use App\Utility\SmsUtility;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OrdersExport;
use App\Models\OrdersExport2;
use Carbon\Carbon;

class RiderController extends Controller
{
    public function riderOrderCollection(Request $request){

        $sort_search = null;
        $delivery_status = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $histories = DeliveryHistory::orderBy('id', 'desc');

        // if ($request->has('search')) {

        //     $sort_search = $request->search;
        //     // $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        //     $histories = $histories->where(function($p) use($sort_search){

        //         $p->whereHas('delivery_boy', function($q) use ($sort_search){
        //                 $q->where("phone", "like", "%$sort_search%");
        //             });

        //     });
        // }

        if($request->has("rider")){

            $rider = $request->rider;

            $histories = $histories->whereHas("rider", function($q) use($rider) {
                $q->where("id", $rider);
            });
        }


        if ($start_date != null) {
            $histories = $histories->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
        }
        if ($end_date != null) {
            $histories = $histories->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
        }



        $histories = $histories->paginate(15);

        $riders = DeliveryBoy::all();

        return view('backend.rider.order-collection', compact('histories', 'sort_search', 'delivery_status', 'start_date', 'end_date', 'riders'));
    }

    public function riderPaymentHistory(Request $request){

        $sort_search = null;
        $delivery_status = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $payments = DeliveryBoyPayment::orderBy('id', 'desc');

        // if ($request->has('search')) {

        //     $sort_search = $request->search;
        //     // $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        //     $payments = $payments->where(function($p) use($sort_search){

        //         $p->whereHas('rider', function($q) use ($sort_search){
        //                 $q->where("name", "like", "%$sort_search%");
        //             });

        //     });
        // }

        if($request->has("rider")){

            $rider = $request->rider;

            $payments = $payments->whereHas('rider', function($q) use ($rider){
                $q->where("id", $rider);
            });

        }


        if ($start_date != null) {
            $payments = $payments->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
        }
        if ($end_date != null) {
            $payments = $payments->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
        }



        $payments = $payments->paginate(15);

        // $riders = User::where("user_type", "delivery_boy")->get();
        $riders = DeliveryBoy::all();

        return view('backend.rider.payment-history', compact('payments', 'sort_search', 'delivery_status', 'start_date', 'end_date', 'riders'));

    }

    public function riderPaymentShow($id){

        $payment = DeliveryBoyPayment::findOrFail($id);

        $rider = DeliveryBoy::findOrFail($payment->rider->id);

        $histories = DeliveryHistory::where("delivery_boy_payment_id", $id)->get();

        return view("backend.rider.payment-show", compact("histories", "payment", "rider"));
    }

    public function markRiderPaymentAsPaid($id){

        DB::beginTransaction();
        try{
            $payment = DeliveryBoyPayment::findOrFail($id);

            $rider = DeliveryBoy::findOrFail($payment->rider->id);

            $payment->status = "paid";
            $payment->save();

            // reset rider earning
            $rider->total_earning = 0;
            $rider->save();

            DB::commit();
            Session::flash("success",translate('Marked as Paid Successfully'));

            return redirect()->route("rider.payment.show", $id);

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

    }
}
