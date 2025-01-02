<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUs;

class SupportPage extends Component
{

    public $name;
    public $email;
    public $phone;
    public $description;

    public $success = false;


    public function render()
    {
        return view('livewire.support-page');
    }

    public function sendMail(){

        $validated = $this->validate([ 
            'name' => 'required',
            'email' => 'required|email|email:rfc,dns',
            'phone' => 'required|min:11|max:11',
            'description' => 'required'
        ]);

        Mail::to('support@muslim.town')->send(new ContactUs($validated));

        $this->success = true;
    }
}
