<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsersImport;
use App\Models\CustomersImport;
use App\Models\User;
use Excel;
use PDF;
use Carbon\Carbon;
use Session;

class CustomerBulkUploadController extends Controller
{
    public function index()
    {
        return view('bulk_upload.customer_upload');
    }

    public function user_bulk_upload(Request $request)
    {
        if($request->hasFile('user_bulk_file')){
            Excel::import(new UsersImport, request()->file('user_bulk_file'));
        }
        Session::flash("success",translate('User exported successfully'));
        return back();
    }

    public function pdf_download_user()
    {
        $users = User::where('created_at','LIKE', '%'. Carbon::today()->toDateString().'%')->get();

        return PDF::loadView('backend.downloads.user',[
            'users' => $users,
        ], [], [])->download('user.pdf');
    }

    public function customer_bulk_file(Request $request)
    {
        if($request->hasFile('customer_bulk_file')){
            Excel::import(new CustomersImport, request()->file('customer_bulk_file'));
        }
        Session::flash("success",translate('Customers exported successfully'));
        return back();
    }
}
