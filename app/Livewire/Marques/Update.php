<?php

namespace App\Livewire\Marques;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;
    public $nom,$logo,$marque;

    public function mount($marque){
        $this->marque = $marque;
        $this->nom = $this->marque->nom;
    }

    public function render()
    {
        return view('livewire.marques.update');
    }


    public function update(){
        $this->validate([
            'nom' =>'required|string|max:200',
            'logo' =>'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $this->marque->nom = $this->nom;
        if($this->logo){
            Storage::disk('public')->delete($this->marque->image); 
            $this->marque->image = $this->logo->store('marques', "public");
        }
        $this->marque->save();


        return redirect('/admin/marques')->with('success', "Marque modifié !");
    }

}
