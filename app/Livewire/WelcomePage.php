<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Component;
use Auth;
use Livewire\Attributes\On;

class WelcomePage extends Component
{
    
    // public $categories = [];
    public $products = [];

    public $search = ''; // This will sync with the query string.

    protected $queryString = ['search'];

    public function mount()
    {
        $this->initializeData();
    }

    
    #[On('refresh-welcome')] 
    public function initializeData(){

        // $categories = Category::orderBy("order_level", "DESC")
        //     ->where("parent_id", 0)
        //     ->where("is_b2b_category", false)
        //     ->where("is_active", true)->get();

        if($this->search){
            $products = Product::orderBy('priority', 'desc')->where("published", 1)->where("name", "like", "%$this->search%")->get()->take(60);
        }else{
            $products = Product::orderBy('priority', 'desc')->where("published", 1)->get()->take(60);
        }

        // $this->categories = $categories; 
        $this->products = $products;

    }

    public function render()
    {
        return view('livewire.welcome-page');
    }

 
}


