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
            if ($marque->image) {
                Storage::disk('public')->delete($marque->image); 
            };
            $marque->delete();
            session()->flash('info', 'Marque supprimée avec succès');
        }
    }
}
