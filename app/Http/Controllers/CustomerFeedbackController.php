<?php

namespace App\Http\Controllers;

use App\Models\CustomerComplainExport;
use App\Models\CustomerFeedback;
use App\Models\CustomerFeedbackExport;
use App\Models\User;
use Illuminate\Http\Request;
use Excel;
use Session;

class CustomerFeedbackController extends Controller
{

    public function index()
    {
        $feedbacks = CustomerFeedback::orderBy('created_at','desc')->get();
        return view('backend.customer_feedback.index', compact('feedbacks'));
    }

    public function create()
    {
        $users = User::where('user_type','customer')->get();
        return view('backend.customer_feedback.create', compact('users'));
    }

    public function export(){
        return Excel::download(new CustomerFeedbackExport, 'feeback.xlsx');
    }

    public function store(Request $request)
    {
        $feedback = CustomerFeedback::create([
            'product' => $request->product,
            'price' => $request->price,
            'delivery' => $request->delivery,
            'user_id' => $request->user_id,
            'note' => $request->note
        ]);

        Session::flash("success",translate('Customer Feedback Create Successfully'));
        return redirect()->route('feedback.index');
    }

    public function show(CustomerFeedback $feedback)
    {
        $feedback->delete();
        Session::flash("success",(translate('Customer Feedback Deleted Successfully')));
        return redirect()->route('feedback.index');
    }

    public function edit(CustomerFeedback $feedback, User $user)
    {
        $users = User::where('user_type','customer')->get();
        return view('backend.customer_feedback.edit',compact('feedback','users'));
    }

    public function update(Request $request, CustomerFeedback $feedback)
    {

        $feedback->update([
            'product' => $request->product,
            'price' => $request->price,
            'delivery' => $request->delivery,
            'user_id' => $request->user_id,
            'note' => $request->note
        ]);

        Session::flash("success",translate('Customer Feedback Create Successfully'));
        return redirect()->route('feedback.index');
    }

    public function destroy(CustomerFeedback $feedback)
    {

        $feedback->delete();
        Session::flash("success",(translate('Customer Feedback Deleted Successfully')));
        return redirect()->route('feedback.index');
    }
}
