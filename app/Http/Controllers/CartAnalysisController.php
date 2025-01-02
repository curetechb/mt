<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CartAnalysisController extends Controller
{

    public function index()
    {
        if(!request('q')){
            $users = User::where('user_type', 'customer')->has('carts')->paginate(10);
        }else{
            $users = User::where('user_type', 'customer')->where('phone', "like", "%".request('q')."%")->has('carts')->paginate(10);
        }
        return view('backend.cart_analysis.index', compact('users'));
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
