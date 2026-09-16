<?php

namespace App\Livewire\Commandes;

use Livewire\Component;
use App\Http\Traits\ListGouvernorats as TraitsListGouvernorats;
use App\Models\commandes;
use App\Models\produits;

use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Mail\{OrderChangeStatut, ChangeStatut};
use App\Models\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class Confirmation extends Component
{

    use WithPagination;
    use TraitsListGouvernorats;

    public $selectedCommandes = [];
    public $date, $statut, $key, $gouvernoratsTunisie, $gouvernorat, $statut2;

    public $commercial_id;
    public $date_debut, $dateDebut;
    public $date_fin, $dateFin;
    public $totauxParCommercial = [];



    public function mount()
    {
        // Récupérer la date depuis la session si elle existe
        $this->date = Session::get('commande_date', null);
        $this->date = session('date') ?? null;
        $this->date_debut = session('date_debut') ?? null;
        $this->date_fin = session('date_fin') ?? null;
    }

    public function updatedDate($value)
    {
        // Stocker la date dans la session chaque fois qu'elle est modifiée
        Session::put('commande_date', $value);
    }

    public function filtrer()
    {
        //reset page
        $this->resetPage();
        Session::put('commande_date', $this->date);
        session(['date' => $this->date]);
        session(['date_debut' => $this->date_debut]);
        session(['date_fin' => $this->date_fin]);
    }

    public function updatedKey($value)
    {
        $this->key = $value;
        $this->resetPage();
    }
    public function resetFilters()
    {
        $this->key = '';
        $this->statut = '';
        $this->statut2 = '';
        $this->commercial_id = '';
        $this->date = '';
        $this->date_debut = '';
        $this->date_fin = '';

        // Recharge les commandes après réinitialisation
        $this->filtrer();
    }

    public function render()
    {

        $commandesQuery = commandes::query();
        // Filtrage selon l'utilisateur connecté
        if (auth()->user()->role != 'admin') {
            $commandesQuery->where('caisse_id', auth()->id());
        } else {

            // ✅ Filtre par caisse (admin seulement)
            if (!empty($this->caisse_id)) {
                $commandesQuery->where('caisse_id', $this->caisse_id);
            }
        }
        $commandesQuery->where('caisse_id', '!=', 'null');
        if (strlen($this->date) > 0) {
            $commandesQuery->whereDate('created_at', $this->date);
        }
        if (strlen($this->date) > 0) {
            $commandesQuery->whereDate('created_at', $this->date);
        }
        if (!empty($this->date)) {
            $dateTime = str_replace('T', ' ', $this->date);
            $commandesQuery->where('created_at', '>=', $dateTime);
        }
        //$commandesQuery->where('statut', '!=', 'payée');
        //    $commandesQuery->where('etat', '!=', 'annulé');
        //  $commandesQuery->where('statut', '!=', 'retournée');
        // Filtre entre date + heure
        if (!empty($this->date_debut) && !empty($this->date_fin)) {
            $debut = str_replace('T', ' ', $this->date_debut);
            $fin = str_replace('T', ' ', $this->date_fin);

            $commandesQuery->whereBetween('created_at', [$debut, $fin]);
        }


        if ($this->dateDebut && $this->dateFin) {
            $commandesQuery->whereBetween('created_at', [$this->dateDebut, $this->dateFin]);
        } elseif ($this->dateDebut) {
            $commandesQuery->whereDate('created_at', '>=', $this->dateDebut);
        } elseif ($this->dateFin) {
            $commandesQuery->whereDate('created_at', '<=', $this->dateFin);
        }

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
        if (strlen($this->key) > 0) {
            $commandesQuery->where('nom', 'like', '%' . $this->key . '%')
                ->orWhere('adresse', 'like', '%' . $this->key . '%')
                ->orWhere('phone', 'like', '%' . $this->key . '%')
                ->orWhere('reference', 'like', '%' . $this->key . '%')

                ->orWhere('prenom', 'like', '%' . $this->key . '%');
        }
        $commandes = $commandesQuery->Orderby('id', "Desc")->paginate(80);


        // Total par commercial
        if ($this->commercial_id) {
            $commandes = $commandesQuery
                ->where('caisse_id', $this->commercial_id)
                ->get();

            $this->totauxParCommercial = $commandes->groupBy('caisse_id')->map(function ($commandesDuCommercial) {
                return [
                    'nom' => $commandesDuCommercial->first()->commercial->nom ?? 'Client',
                    'total' => $commandesDuCommercial->sum(function ($commande) {
                        return ($commande->montant() - ($commande->coupon ?? 0));
                    }),
                ];
            })->toArray();
        } else {
            $this->totauxParCommercial = []; // Rien à afficher si aucun commercial sélectionné
        }




        //dd($this->totauxParCommercial);
        $total = commandes::count();
      /*   $commerciaux = auth()->user()->role == 'admin'
            ? User::where('role', 'commercial')->get()
            : []; */
            $commerciaux = auth()->user()->role == 'admin'
    ? User::whereNotIn('role', ['client', 'admin'])->get()
    : collect();
        return view('livewire.commandes.confirmation', compact("commandes", "total", "commerciaux"));
    }
}
