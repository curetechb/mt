<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{

    public function index()
    {
        return view('home');
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        Mail::to('support@muslim.town')->send(new ContactUs($data));
        // print_r($request->all());
        dd('sent');
    }


    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
