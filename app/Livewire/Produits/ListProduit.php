<?php

namespace App\Livewire\Produits;

use App\Models\produits;
use App\Models\Shop;
use App\Models\historiques_stock;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination; // 🔹 Assurez-vous que cette ligne est présente et non commentée

class ListProduit extends Component
{
    use WithPagination; // 🔹 INDISPENSABLE pour que $this->resetPage() et paginate() fonctionnent

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['add-stock' => '$refresh'];
    
    public $key = '';
    public $showModal = false;
    public $selectedProduit;
    public $stock = 1; 
    public $shop_id; 
    public $shops;   
    public $total_supprimers = 0; 
 
    public function mount()
    {
        $this->shops = Shop::all();
    }

    public function updatingKey()
    {
        $this->resetPage();
    }

    public function render()
    {
        $Query = produits::query();

        if (!empty($this->key)) {
            $Query->where(function($q) {
                $q->where('nom', 'like', '%' . $this->key . '%')
                  ->orWhere('reference', 'like', '%' . $this->key . '%');
            });
        }

        $produits = $Query->paginate(30);
        $total = produits::count();
        $this->total_supprimers = produits::onlyTrashed()->count();
        
        return view('livewire.produits.list-produit', compact('produits', 'total'));
    }

    public function openModal($produitId)
    {
        $this->selectedProduit = $produitId; 
        $this->stock = 1; 
        $this->shop_id = null; 
        $this->showModal = true; 
    }

    public function addStock()
    {
        if (!$this->shop_id) {
            session()->flash('error', 'Veuillez sélectionner une boutique.');
            return;
        }

        if (!$this->stock || $this->stock <= 0) {
            session()->flash('error', 'Veuillez saisir une quantité valide.');
            return;
        }

        $produit = produits::find($this->selectedProduit);
        if ($produit) {
            $pivot = $produit->shops()->where('shop_id', $this->shop_id)->first();
            $ancienStockParticulier = $pivot ? $pivot->pivot->stock_particulier : 0;

            $produit->shops()->syncWithoutDetaching([
                $this->shop_id => [
                    'stock_particulier' => $ancienStockParticulier + $this->stock
                ]
            ]);

            $nouveauStockGeneral = DB::table('product_shop')
                ->where('produit_id', $produit->id)
                ->sum('stock_particulier');

            $produit->stock = $nouveauStockGeneral;
            $produit->save();
            
            $historique_stock = new historiques_stock();
            $historique_stock->quantite = $this->stock;
            $historique_stock->id_produit = $produit->id;
            $historique_stock->shop_id = $this->shop_id; 
            $historique_stock->save();

            session()->flash('message', 'Stock ajouté avec succès.');
            $this->showModal = false; 
        }
    }

    public function delete($id)
    {
        $produit = produits::find($id);
        if ($produit) {
            $produit->delete();
            session()->flash('info', 'Produit supprimé avec succès');
        }
    }

    public function add_top($id)
    {
        $produit = produits::find($id);
        if ($produit) {
            $produit->top = ($produit->top == 1) ? 0 : 1;
            $produit->save();
        }
    }

    public function filtrer()
    {
        $this->resetPage();
    }
}