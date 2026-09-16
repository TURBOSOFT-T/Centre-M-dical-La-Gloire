<?php

namespace App\Livewire\Promotions;

use App\Models\categories;
use App\Models\produits;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\promotions as ModelPRomotion;

class Promotions extends Component
{

    public $titre, $pourcentage, $debut, $fin, $all, $produit;


    public function mount($produit)
    {
        $this->produit = $produit;
    }

    public function render()
    {
        $promotions = ModelPRomotion::orderBy('id','Desc')->get();
        return view('livewire.promotions.promotions', compact("promotions"));
    }

    public function create()
    {
        //validation
        $this->validate([
            'titre' => 'required|string',
            'pourcentage' => 'required|integer|between:2,100',
            'debut' => 'required|date',
            'fin' => 'required|date|after:debut',
        ]);

        $promotions = new ModelPRomotion();
        $promotions->titre = $this->titre;
        $promotions->pourcentage = $this->pourcentage;
        $promotions->debut = $this->debut;
        $promotions->fin = $this->fin;
        if (Carbon::parse($this->debut)->equalTo(Carbon::today())) {
            $promotions->statut = "En cours";
        }
        if ($promotions->save()) {
            if ($this->all) {
                $produits = produits::all();
                foreach ($produits as $produit) {
                    $produit->id_promotion = $promotions->id;
                    $produit->save();
                }
            }

            if ($this->produit) {
                $this->produit->id_promotion = $promotions->id;
                $this->produit->save();
            }

            //success message
            session()->flash('success', 'Promotions ajoutées avec succès');
        } else {
            //error message
            session()->flash('error', 'Une erreur est survenue lors de l\'ajout de la promotion');
        }
    }


    public function delete($id)
    {
        $promotions = ModelPRomotion::find($id);
        if ($promotions) {
            $promotions->delete();
            //flash message
            session()->flash('success', 'Promotions supprimées avec succès');
        }
    }
}
