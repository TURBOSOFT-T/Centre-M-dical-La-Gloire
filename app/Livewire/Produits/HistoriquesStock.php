<?php

namespace App\Livewire\Produits;

use App\Models\historiques_stock;
use Livewire\Component;

class HistoriquesStock extends Component
{
    public $produit,$debut,$fin;

    public function mount($produit){
        $this->produit = $produit;
    }

    public function render()
    {
        $Query = historiques_stock::orderby('id','Desc');
        if(!is_null($this->debut)){
            $Query = $Query->whereDate('created_at',$this->debut);
        }

        $historiques = $Query->where('id_produit',$this->produit->id)->paginate(50);
        return view('livewire.produits.historiques-stock', compact("historiques"));
    }

    public function updateDebut($value){
        $this->debut = $value;
        $this->resetPage();
    }
}
