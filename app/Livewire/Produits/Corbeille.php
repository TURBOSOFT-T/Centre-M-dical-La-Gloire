<?php

namespace App\Livewire\Produits;

use App\Models\produits;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Corbeille extends Component
{
    use WithPagination;

    public function render()
    {
        $produits = produits::onlyTrashed()->paginate(30);
        $total = produits::onlyTrashed()->count();
        
        return view('livewire.produits.corbeille', compact('produits', 'total'));
    }

    public function restore($id)
    {
        $produit = produits::onlyTrashed()->find($id);
        
        if ($produit) {
            $produit->restore();
            $this->resetPage();

            session()->flash('success', 'Produit restauré avec succès');
        }
    }

    public function delete_definitif($id)
    {
        $produit = produits::onlyTrashed()->find($id);
        
        if ($produit) {
            // 1. Suppression de la photo principale
            if ($produit->photo && Storage::disk('public')->exists($produit->photo)) {
                Storage::disk('public')->delete($produit->photo);
            }

            // 2. Suppression des photos de la galerie (JSON)
            $galeriePhotos = json_decode($produit->photos, true);
            if (is_array($galeriePhotos)) {
                foreach ($galeriePhotos as $photoPath) {
                    if (Storage::disk('public')->exists($photoPath)) {
                        Storage::disk('public')->delete($photoPath);
                    }
                }
            }

            // 3. Suppression définitive de l'enregistrement en base de données
            $produit->forceDelete();
            
            session()->flash('danger', 'Suppression définitive effectuée avec succès');
        }
    }
}