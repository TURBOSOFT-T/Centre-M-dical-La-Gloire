<?php

namespace App\Livewire\Commandes;
use App\Http\Traits\ListGouvernorats as TraitsListGouvernorats;
use App\Models\commandes;
use App\Models\produits;

use Livewire\WithPagination;
use App\Mail\{OrderChangeStatut, ChangeStatut};
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

use Livewire\Component;

class Lives extends Component
{

    use WithPagination;
    use TraitsListGouvernorats;

    public $selectedCommandes = [];
    public $date, $statut, $key, $gouvernoratsTunisie, $gouvernorat, $statut2;

    public function updatedKey($value)
    {
        $this->key = $value;
        $this->resetPage();
    }


    public function render()
    { 
        $commandesQuery = commandes::query();
        
        if (strlen($this->date) > 0) {
            $commandesQuery->whereDate('created_at', $this->date);
        }
         $commandesQuery->where('statut', '!=', 'payée'); 
          $commandesQuery->where('etat', '!=', 'annulé'); 
           $commandesQuery->where('statut', '!=', 'retournée'); 
      
        if (strlen($this->statut) > 0) {
            $commandesQuery->where('statut', $this->statut);
        }
        if (strlen($this->statut2) > 0) {
            if ($this->statut2 == "confirmer") {
                $commandesQuery->where('etat', "confirmé");
            } else {
                $commandesQuery->where('etat', "annulé");
            }
        }
        
        $commandes = $commandesQuery->Orderby('id', "Desc")->paginate(80);
        $total = commandes::count();
       
        return view('livewire.commandes.lives' , compact("commandes", "total"));
    }
}
