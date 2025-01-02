<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class Purchased extends Component
{

    public $order_id;

    public function mount($order_id){
        $this->order_id = $order_id;
    }

    public function render()
    {
        $order = Order::findOrFail($this->order_id);
        return view('livewire.purchased', compact('order'));
    }
}
