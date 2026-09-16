<?php

namespace App\Livewire\Commandes;

use App\Http\Traits\ListGouvernorats as TraitsListGouvernorats;
use App\Models\commandes;
use App\Models\produits;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Mail\{OrderChangeStatut, ChangeStatut};
use App\Models\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class Commerciaux extends Component
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
            $commandesQuery->where('commercial_id', auth()->id());
        } else {
            // Filtre par commercial (admin seulement)
            if (!empty($this->commercial_id)) {
                $commandesQuery->where('commercial_id', $this->commercial_id);
            }
        }
        
        $commandesQuery->whereNotNull('commercial_id');

        // Filtres de date
        if (!empty($this->date)) {
            $dateTime = str_replace('T', ' ', $this->date);
            $commandesQuery->where('created_at', '>=', $dateTime);
        }

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

        // Filtres de statuts
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

        // Recherche par mots-clés
        if (strlen($this->key) > 0) {
            $commandesQuery->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->key . '%')
                  ->orWhere('adresse', 'like', '%' . $this->key . '%')
                  ->orWhere('phone', 'like', '%' . $this->key . '%')
                  ->orWhere('reference', 'like', '%' . $this->key . '%')
                  ->orWhere('prenom', 'like', '%' . $this->key . '%');
            });
        }

        // 1. Pagination pour l'affichage de la liste
        $commandes = (clone $commandesQuery)->orderBy('id', "Desc")->paginate(80);

        // 2. Calcul du cumul des commissions sur l'ensemble des commandes filtrées
        $commandesPourCommission = (clone $commandesQuery)->with('contenus.produit')->get();
        $totalCommissionsGlobal = 0;
        
        foreach ($commandesPourCommission as $cmd) {
            foreach ($cmd->contenus as $contenu) {
                if ($contenu->produit && $contenu->produit->avec_commission && $contenu->produit->commission) {
                    $totalCommissionsGlobal += $contenu->produit->commission * $contenu->quantite;
                }
            }
        }

        // 3. Total par commercial
        if (!empty($this->commercial_id)) {
            $commandesPourTotaux = (clone $commandesQuery)->get();

            $this->totauxParCommercial = $commandesPourTotaux->groupBy('commercial_id')->map(function ($commandesDuCommercial) {
                return [
                    'nom' => $commandesDuCommercial->first()->commercial->nom ?? 'Client',
                    'total' => $commandesDuCommercial->sum(function ($commande) {
                        return (method_exists($commande, 'montant') ? $commande->montant() : $commande->montant_total) - ($commande->coupon ?? 0);
                    }),
                ];
            })->toArray();
        } else {
            $this->totauxParCommercial = [];
        }

        $total = commandes::count();
        $commerciaux = auth()->user()->role == 'admin'
            ? User::where('role', 'commercial')->get()
            : [];

        return view('livewire.commandes.commerciaux', compact("commandes", "total", "commerciaux", "totalCommissionsGlobal"));
    }


    public function updateStatus($commandeId, $newStatus)
    {

        $commande = commandes::findOrFail($commandeId);
        if ($commande) {
            $commande->statut = $newStatus;


            if ($newStatus == "retournée") {
                foreach ($commande->contenus as $contenus) {
                    $article = produits::find($contenus->id_produit);
                    if ($article) {
                        $article->retourner_stock($contenus->quantite);
                    }
                }
                // $this->sendOrderConfirmationMail($commande);
            }
            if ($newStatus == "En cours livraison") {

                // $this->sendOrderConfirmationMail($commande);
            }

            if ($newStatus == "traitement") {

                //  $this->sendOrderConfirmationMail($commande);
            }
            if ($newStatus == "planification") {

                //  $this->sendOrderConfirmationMail($commande);
            }


            $commande->save();
        }
    }



    public function sendOrderConfirmationMail($commande)
    {
        try {
            //   Mail::to($commande->email)->send(new OrderChangeStatut($commande));
        } catch (\Exception $e) {

            /// \Log::error('Erreur lors de l\'envoi de l\'email de confirmation de commande : ' . $e->getMessage());
        }
    }


    public function delete($id)
    {
        $commande = commandes::find($id);

        // $commande->delete();
        if ($commande->statut == "attente" || $commande->statut == "créé" || $commande->statut == "traitement" || $commande->statut == "planification" || $commande->statut == "livrée") {

            foreach ($commande->contenus as $contenus) {
                $article = produits::find($contenus->id_produit);
                if ($article) {
                    $article->retourner_stock($contenus->quantite);
                }
            }


            $commande->delete();

            //flash message
            session()->flash('success', 'Commande supprimée avec succès');
        }
        return view('livewire.commandes.list-commande');
    }



    public function confirmer($id)
    {
        $commande = commandes::find($id);
        if ($commande) {

            $commande->etat = "confirmé";

            $commande->save();
            $this->sendOrderConfirmationMail($commande);
        }
    }

    public function annuler($id)
    {
        $commande = commandes::find($id);
        if ($commande) {
            foreach ($commande->contenus as $contenus) {
                $article = produits::find($contenus->id_produit);
                if ($article) {
                    $article->retourner_stock($contenus->quantite);
                }
            }
            $commande->statut = "retournée";
            $commande->etat = "annulé";

            $commande->save();
            //  $this->sendOrderConfirmationMail($commande);
        }
    }


    public function toggleCommandeSelection($commandeId)
    {
        if (in_array($commandeId, $this->selectedCommandes)) {
            $this->selectedCommandes = array_diff($this->selectedCommandes, [$commandeId]);
        } else {
            $this->selectedCommandes[] = $commandeId;
        }
    }


    public function getSelectedCommandes()
    {
        //check if $this->selectedCommandes is not empty
        if (count($this->selectedCommandes) > 0) {
            $ids = json_encode($this->selectedCommandes);
            return redirect()->route('print_bordereau', ["ids" => $ids]);
        } else {
            return false;
        }
    }
}
