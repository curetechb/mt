<?php

namespace App\Livewire;

use Livewire\Component;

class ECard extends Component
{

    public $occasion = "Eid";
    public $to = "";    
    public $image = "";
    public $images = [
        "Eid" => "livewire/greetings/eid-ul-fitr.jpg",
        "Ramadan" => "livewire/greetings/ramadan.jpg",
        "Anniversary" => "livewire/greetings/anniversary.png",
        "Birthday" => "livewire/greetings/birthday.jpg",
        "Get well" => "livewire/greetings/getwell.jpg",
        "Thank you" => "livewire/greetings/thank-you.jpg",
        "Congratulation" => "livewire/greetings/congratulations.jpg",
    ];


    public function mount(){
        $this->image = asset("livewire/greetings/ramadan.jpg");
    }

    public function render()
    {
        return view('livewire.e-card');
    }

    public function updateGreetingCard(){
        
        $this->render();
    }

    public function updateOccasion(){
        
        $this->image = $this->images[$this->occasion];
        $this->render();
    }
}
