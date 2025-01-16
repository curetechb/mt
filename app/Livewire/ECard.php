<?php

namespace App\Livewire;

use Livewire\Component;

class ECard extends Component
{

    public $occasion = "Ramadan";
    public $to = "";    
    public $image = "";
    public $images = [
        "Ramadan" => "livewire/greetings/ramadan.jpg",
        "Anniversary" => "livewire/greetings/anniversary.png",
        "Eid-ul-Fitr" => "livewire/greetings/eid-ul-fitr.jpg",
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
