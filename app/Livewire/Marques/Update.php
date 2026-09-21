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
         
        ]);

        $this->marque->nom = $this->nom;
       
        $this->marque->save();


        return redirect('/admin/laboratoires')->with('success', "Labo modifié !");
    }

}
