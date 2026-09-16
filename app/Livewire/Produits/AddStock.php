<?php

namespace App\Livewire\Produits;

use App\Models\historiques_stock;
use App\Models\produits;
use App\Models\Shop; // Importation du modèle Shop
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddStock extends Component
{
    public $produit, $produits, $id, $quantite;
    public $shop_id; // Stocke le shop sélectionné
    public $shops;   // Liste des shops affichés dans le select

    public function mount()
    {
        // Récupère toutes les boutiques actives pour remplir le menu déroulant
        $this->shops = Shop::all();
    }

    public function updatedProduit($value)
    {
        $this->id = null;
        $this->quantite = null;

        $this->produits = produits::where('nom', 'like', '%' . $value . '%')
            ->orWhere('reference', 'like', '%' . $value . '%')
            ->select('id', 'nom', 'photo')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.produits.add-stock');
    }

    public function copier($id)
    {
        $pro = produits::find($id);
        if ($pro) {
            $this->id = $id;
        }
    }

    public function add()
    {
        // Validations basiques
        if (!$this->id) {
            session()->flash('error', 'Veuillez sélectionner un produit');
            return;
        }

        if (!$this->shop_id) {
            session()->flash('error', 'Veuillez sélectionner une boutique');
            return;
        }

        if (!$this->quantite || $this->quantite <= 0) {
            session()->flash('error', 'Veuillez saisir une quantité valide');
            return;
        }

        $pro = produits::find($this->id);
        if (!$pro) {
            session()->flash('error', 'Le produit sélectionné n\'existe pas.');
            return;
        }

        // 1. Mise à jour ou création du stock particulier dans la table pivot
        $pivot = $pro->shops()->where('shop_id', $this->shop_id)->first();
        $ancienStockParticulier = $pivot ? $pivot->pivot->stock_particulier : 0;
        
        $pro->shops()->syncWithoutDetaching([
            $this->shop_id => [
                'stock_particulier' => $ancienStockParticulier + $this->quantite
            ]
        ]);

        // 2. Recalcul et mise à jour du stock général du produit
        $nouveauStockGeneral = DB::table('product_shop')
            ->where('produit_id', $pro->id)
            ->sum('stock_particulier');

        $pro->stock = $nouveauStockGeneral;
        $pro->save();

        // 3. Enregistrer l'historique du stock (en y liant le shop_id)
        $historique_stock = new historiques_stock();
        $historique_stock->quantite = $this->quantite;
        $historique_stock->id_produit = $pro->id;
        $historique_stock->shop_id = $this->shop_id; // Pensez à vérifier si votre table historique possède cette colonne
        $historique_stock->save();

        // Reset des inputs
        $this->produit = null;
        $this->quantite = null;
        $this->shop_id = null;
        $this->id = null;

        session()->flash('success', 'Stock ajouté avec succès');
        $this->dispatch('add-stock');
    }
}