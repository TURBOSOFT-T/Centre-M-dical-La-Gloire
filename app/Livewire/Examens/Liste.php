<?php

namespace App\Livewire\Examens;

use App\Models\Examen;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Liste extends Component
{

    protected $listeners = ['ExamenAdded' => '$refresh'];

    public function render()
    {
        $examens = Examen::orderby('id','desc')->get();
        return view('livewire.examens.liste', compact("examens"));
    }


    public function delete($id){
        $examen = Examen::find($id);
        if ($examen) {
          
        
            $examen->delete();
            session()->flash('info', 'Examen supprimée avec succès');
        }
    }
}
