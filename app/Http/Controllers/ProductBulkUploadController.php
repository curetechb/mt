<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ChalProductsImport;
use App\Models\User;
use Auth;
use App\Models\ProductsImport;
use App\Models\ProductsExport;
use App\Models\ProductImportUpdate;
use PDF;
use Excel;
use Session;

class ProductBulkUploadController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == 'seller') {
            if(Auth::user()->seller->verification_status){
                return view('frontend.user.seller.product_bulk_upload.index');
            }
            else{
                Session::flash("error",'Your shop is not verified yet!');
                return back();
            }
        }
        elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.product.bulk_upload.index');
        }
    }


    public function export(){
        return Excel::download(new ProductsExport, 'products.xlsx');
    }


    public function import(Request $request){

        ini_set('max_execution_time', 300);

        if($request->hasFile('bulk_file')){
            $import = new ProductImportUpdate;
            Excel::import($import, request()->file('bulk_file'));
        }

        return back();
    }

    public function pdf_download_category()
    {
        $categories = Category::all();

        return PDF::loadView('backend.downloads.category',[
            'categories' => $categories,
        ], [], [])->download('category.pdf');
    }

    public function pdf_download_brand()
    {
        $brands = Brand::all();

        return PDF::loadView('backend.downloads.brand',[
            'brands' => $brands,
        ], [], [])->download('brands.pdf');
    }

    public function pdf_download_seller()
    {
        $users = User::where('user_type','seller')->get();

        return PDF::loadView('backend.downloads.user',[
            'users' => $users,
        ], [], [])->download('user.pdf');

    }

    public function bulk_upload(Request $request)
    {

        if($request->hasFile('bulk_file')){
            // $import = new ProductsImport();
            // ini_set('upload_max_size', '64M');
            // ini_set('post_max_size', '64M');
            // ini_set('max_execution_time', 300);

            // $import = new ChalProductsImport();
            $import = new ProductsImport();
            Excel::import($import, request()->file('bulk_file'));
        }

        return back();
    }

}
