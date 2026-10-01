<?php

namespace App\Livewire\Patients;

use App\Models\Patient;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Corbeille extends Component
{
    use WithPagination;

    public function render()
    {
        $patients = Patient::onlyTrashed()->paginate(30);
        $total = Patient::onlyTrashed()->count();
        
        return view('livewire.patients.corbeille', compact('patients', 'total'));
    }

    public function restore($id)
    {
        $produit = Patient::onlyTrashed()->find($id);
        
        if ($produit) {
            $produit->restore();
            $this->resetPage();

            session()->flash('success','Patient restauré avec succès');
        }
    }

    public function delete_definitif($id)
    {
        $produit = Patient::onlyTrashed()->find($id);
        
        if ($produit) {
          

          

            // 3. Suppression définitive de l'enregistrement en base de données
            $produit->forceDelete();
            
            session()->flash('danger', 'Suppression définitive effectuée avec succès');
        }
    }
}