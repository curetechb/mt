<?php

namespace App\Livewire;

use Livewire\Component;

class Header extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.header');
    }

    public function submitSearchForm(){
     
        return $this->redirect("/?search=$this->search", navigate: true);
    }
}
