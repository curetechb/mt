<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use Session;

class PolicyController extends Controller
{

    public function index($type)
    {
        $policy = Policy::where('name', $type)->first();
        return view('policies.index', compact('policy'));
    }

    //updates the policy pages
    public function store(Request $request){
        $policy = Policy::where('name', $request->name)->first();
        $policy->name = $request->name;
        $policy->content = $request->content;
        $policy->save();

        Session::flash("success",$request->name.' '.translate('updated successfully'));
        return back();
    }
}
