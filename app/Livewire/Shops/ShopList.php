<?php

namespace App\Livewire\Shops;

use Livewire\Component;
use App\Models\produits;
use App\Models\config;
use App\Models\Category;

class ShopList extends Component
{
    public $produits, $categories, $configs;
    public function render()


    {
       
        $produits = produits::all();
        $categories = Category::with('produits')->get();
        $configs= config::all();
        return view('livewire.shops.shop-list', compact('produits','categories','configs'));
    }
}
