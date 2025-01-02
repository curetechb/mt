<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class ShowPage extends Component
{
    public $slug;

    public function mount($slug){
        $this->slug = $slug;

    }

    public function render()
    {
        $page = Page::where('slug', $this->slug)->first();
        return view('livewire.show-page', compact('page'));
    }
}
