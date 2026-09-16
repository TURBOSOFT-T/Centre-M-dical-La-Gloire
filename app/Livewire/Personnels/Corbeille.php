<?php

namespace App\Livewire\Personnels;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Corbeille extends Component
{
    use WithPagination;

    public function render()
    {
        $personnels = User::onlyTrashed()->paginate(30);
        $total = User::onlyTrashed()->count();
        
        return view('livewire.personnels.corbeille', compact('personnels', 'total'));
    }

    public function restore($id)
    {
        $personnel = User::onlyTrashed()->find($id);
        
        if ($personnel) {
            $personnel->restore();
            $this->resetPage();

            session()->flash('success', 'Personnel restauré avec succès');
        }
    }

    public function delete_definitif($id)
    {
        $personnel = User::onlyTrashed()->find($id);
        
        if ($personnel ) {
            // 1. Suppression de la photo principale
          


            // 3. Suppression définitive de l'enregistrement en base de données
            $personnel->forceDelete();
            
            session()->flash('danger', 'Suppression définitive effectuée avec succès');
        }
    }
}