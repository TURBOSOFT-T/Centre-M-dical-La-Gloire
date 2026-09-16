<?php

namespace App\Livewire\Produits;

use App\Models\produits;
use App\Models\Shop; // Importation du modèle Shop
use App\Models\historiques_stock;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListProduit extends Component
{
    protected $listeners = ['add-stock' => '$refresh'];
    use WithPagination;
    
  public $key = '';
    public $showModal = false;
    public $selectedProduit;
    public $stock = 1; 
    public $shop_id; // Stocke la boutique sélectionnée dans le modal
    public $shops;   // Liste des boutiques pour le select du modal

    public function mount()
    {
        // Récupère les boutiques actives pour alimenter le modal
        $this->shops = Shop::all();
    }
public function updatingKey()
    {
        $this->resetPage();
    }
    public function render()
    {
        $Query = produits::query();
        if(!is_null($this->key)){
            $Query->where('nom', 'like', '%'.$this->key.'%');
          //   $Query->where('reference', 'like', '%'.$this->key.'%');
        }
        $produits = $Query->paginate(30);
        $total = produits::count();
        $total_supprimers = produits::onlyTrashed()->count();
        
        return view('livewire.produits.list-produit', compact('produits', 'total', 'total_supprimers'));
    }

    public function openModal($produitId)
    {
        $this->selectedProduit = $produitId; 
        $this->stock = 1; 
        $this->shop_id = null; // Réinitialise la boutique sélectionnée
        $this->showModal = true; 
    }

    public function addStock()
    {
        // Validations
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
            // 1. Mise à jour ou création du stock dans la table pivot
            $pivot = $produit->shops()->where('shop_id', $this->shop_id)->first();
            $ancienStockParticulier = $pivot ? $pivot->pivot->stock_particulier : 0;

            $produit->shops()->syncWithoutDetaching([
                $this->shop_id => [
                    'stock_particulier' => $ancienStockParticulier + $this->stock
                ]
            ]);

            // 2. Recalcul et mise à jour du stock général global du produit
            $nouveauStockGeneral = DB::table('product_shop')
                ->where('produit_id', $produit->id)
                ->sum('stock_particulier');

            $produit->stock = $nouveauStockGeneral;
            $produit->save();
            
            // 3. Enregistrement de l'historique lié au produit et au shop
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