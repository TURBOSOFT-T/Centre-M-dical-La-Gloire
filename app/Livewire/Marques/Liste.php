<?php

namespace App\Livewire\Marques;

use App\Models\Marque;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Liste extends Component
{

    protected $listeners = ['MarqueAdded' => '$refresh'];

    public function render()
    {
        $marques = Marque::orderby('id','desc')->get();
        return view('livewire.marques.liste', compact("marques"));
    }


    public function delete($id){
        $marque = Marque::find($id);
        if ($marque) {
          
        
            $marque->delete();
            session()->flash('info', 'Marque supprimée avec succès');
        }
    }
}
