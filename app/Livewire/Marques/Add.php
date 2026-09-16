<?php

namespace App\Livewire\Marques;

use App\Models\Marque;
use Livewire\Component;
use Livewire\WithFileUploads;


class Add extends Component
{

    use WithFileUploads;
    public $nom,$logo;

    public function render()
    {
        return view('livewire.marques.add');
    }


    public function save(){
        $this->validate([
            'nom' =>'required|string|max:200',
            'logo' =>'required|image|mimes:jpg,jpeg,png,webp',
        ]);

        $marque = new Marque();
        $marque->nom = $this->nom;
        $marque->image = $this->logo->store('marques', 'public');
        $marque->save();

        session()->flash('success', 'Marque ajoutée avec succès');
        $this->reset();
        $this->dispatch("MarqueAdded");
    }

}
