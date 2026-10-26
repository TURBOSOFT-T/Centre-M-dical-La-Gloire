<?php

namespace App\Livewire\Commandes;

use App\Http\Traits\ListGouvernorats as TraitsListGouvernorats;
use App\Models\commandes;
use App\Models\produits;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ListCommande extends Component
{
    use WithPagination;
    use TraitsListGouvernorats;

    protected $paginationTheme = 'bootstrap';

    public $selectedCommandes = [];
    public $date, $statut, $key, $commercial_id, $date_debut, $date_fin, $statut2;
    
    // 🗓️ Propriété pour le filtre par période rapide
    public $filtre_periode = ''; // 'jour', 'semaine', 'mois'

    private function applyShopRestriction($query)
    {
        $user = auth()->user();
        if (isset($user->shop_id) && !empty($user->shop_id)) {
            $query->where('shop_id', $user->shop_id);
        }
        return $query;
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
        $this->filtre_periode = '';
        $this->selectedCommandes = [];

        $this->resetPage();
    }

    public function filtrer()
    {
        $this->resetPage();
    }

    public function toggleCommandeSelection($id)
    {
        if (in_array($id, $this->selectedCommandes)) {
            $this->selectedCommandes = array_diff($this->selectedCommandes, [$id]);
        } else {
            $this->selectedCommandes[] = $id;
        }
    }

    public function getSelectedCommandes()
    {
        session()->flash('success', count($this->selectedCommandes) . ' commande(s) sélectionnée(s) pour export.');
    }

    public function render()
    {
        $commandesQuery = commandes::query();

        // 🔒 Restriction par boutique
        $this->applyShopRestriction($commandesQuery);

        $user = auth()->user();

        // 👤 Gestion des rôles et visibilité des commandes
        if ($user->role == 'caisse') {
            // Le caissier voit ses propres commandes confirmées OU celles en attente/non confirmées
            $commandesQuery->where(function($q) use ($user) {
                $q->where('caisse_id', $user->id)
                  ->orWhereNull('caisse_id')
                  ->orWhere('etat', '!=', 'confirmé');
            });
        } elseif ($user->role == 'admin' && !empty($this->commercial_id)) {
            // Filtrage par caisse sélectionnée (via le select 'commercial_id')
            $commandesQuery->where('caisse_id', $this->commercial_id);
        }

        // 📅 Filtre par Période Rapide (Jour, Semaine, Mois)
        if (!empty($this->filtre_periode)) {
            switch ($this->filtre_periode) {
                case 'jour':
                    $commandesQuery->whereDate('created_at', Carbon::today());
                    break;
                case 'semaine':
                    $commandesQuery->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'mois':
                    $commandesQuery->whereMonth('created_at', Carbon::now()->month)
                                  ->whereYear('created_at', Carbon::now()->year);
                    break;
            }
        } 
        // 📅 Filtres de dates classiques (Date exacte ou Intervalle)
        elseif (!empty($this->date)) {
            $commandesQuery->whereDate('created_at', $this->date);
        }

        if (!empty($this->date_debut) && !empty($this->date_fin)) {
            $debut = str_replace('T', ' ', $this->date_debut);
            $fin = str_replace('T', ' ', $this->date_fin);
            $commandesQuery->whereBetween('created_at', [$debut, $fin]);
        }

        // ⚙️ Filtre par État de la commande
        if (strlen($this->statut) > 0) {
            $commandesQuery->where('statut', $this->statut);
        }

        // ⚙️ Filtre par Confirmation (statut2)
        if (strlen($this->statut2) > 0) {
            if ($this->statut2 == "confirmé") {
                $commandesQuery->where('etat', "confirmé");
            } else {
                $commandesQuery->where('etat', "!=", "confirmé");
            }
        }

        // 🔍 Recherche Multiterme
        if (strlen($this->key) > 0) {
            $commandesQuery->where(function($q) {
                $q->where('nom', 'like', '%' . $this->key . '%')
                  ->orWhere('adresse', 'like', '%' . $this->key . '%')
                  ->orWhere('phone', 'like', '%' . $this->key . '%')
                  ->orWhere('reference', 'like', '%' . $this->key . '%')
                  ->orWhere('prenom', 'like', '%' . $this->key . '%');
            });
        }

        // 📊 Calcul des totaux par caissier (pour l'évaluation administrative)
        $totauxParCommercial = [];
        if ($user->role == 'admin') {
            $rawTotaux = (clone $commandesQuery)
                ->select('caisse_id', DB::raw('SUM(montant_total) as total'))
                ->groupBy('caisse_id')
                ->with('caissier')
                ->get();

            foreach ($rawTotaux as $row) {
                if ($row->caisse_id) {
                    $caisseNom = $row->caissier->nom ?? 'Inconnu';
                    $totauxParCommercial[$row->caisse_id] = [
                        'nom' => $caisseNom,
                        'total' => $row->total ?? 0
                    ];
                }
            }
        }

        $commandes = $commandesQuery->orderBy('id', 'desc')->paginate(10);
        $total = $this->applyShopRestriction(commandes::query())->count();

        // Liste des caissiers pour le select
        $caisses = User::where('role', 'caisse')->get();

        return view('livewire.commandes.list-commande', compact("commandes", "total", "caisses", "totauxParCommercial"));
    }

    public function updateStatus($commandeId, $newStatus)
    {
        $commande = $this->applyShopRestriction(commandes::query())->findOrFail($commandeId);
        $user = auth()->user();

        if ($commande->etat === 'confirmé' && $commande->caisse_id != $user->id && $user->role != 'admin') {
            session()->flash('warning', 'Vous ne pouvez pas modifier cette commande confirmée par un autre caissier.');
            return;
        }

        $commande->statut = $newStatus;

        if ($newStatus == "retournée") {
            foreach ($commande->contenus as $contenu) {
                $article = produits::find($contenu->id_produit);
                if ($article) {
                    $article->retourner_stock($contenu->quantite, $commande->shop_id);
                }
            }
        }

        if ($newStatus == "payée") {
            $telephoneClient = $commande->phone ?? null;
            if ($telephoneClient) {
                $nomClient = $commande->nom ?? 'Client';
                $messageTexte = "Bonjour " . $nomClient . ", votre commande #" . $commande->reference . " a ete payee avec succes.";
                
                try {
                    Http::get('https://sms.etech-keys.com/ss/api.php', [
                        'login'        => '655262413',
                        'password'     => '655262413',
                        'sender_id'    => 'ETECH KEYS',
                        'destinataire' => $telephoneClient,
                        'message'      => $messageTexte,
                        'ext_id'       => $commande->id,
                        'programmation'=> 0
                    ]);
                } catch (\Exception $e) {
                    Log::error("Exception SMS commande #" . $commande->id . " : " . $e->getMessage());
                }
            }
        }

        $commande->save();
        session()->flash('success', 'Le statut de la commande a bien été mis à jour.');
    }

    public function confirmer($id)
    {
        $commande = $this->applyShopRestriction(commandes::query())->find($id);
        $user = auth()->user();

        if ($commande) {
            $commande->etat = "confirmé";
            $commande->caisse_id = $user->id;
            $commande->save();
            session()->flash('success', 'Commande confirmée avec succès.');
        }
    }

    public function annuler($id)
    {
        $commande = $this->applyShopRestriction(commandes::query())->find($id);
        $user = auth()->user();

        if ($commande) {
            foreach ($commande->contenus as $contenu) {
                $article = produits::find($contenu->id_produit);
                if ($article) {
                    $article->retourner_stock($contenu->quantite, $commande->shop_id);
                }
            }
            $commande->statut = "retournée";
            $commande->etat = "annulé";
            $commande->caisse_id = $user->id;
            $commande->save();

            session()->flash('info', 'Commande annulée et stock restitué.');
        }
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $commande = $this->applyShopRestriction(commandes::query())->findOrFail($id);

            if (!in_array($commande->statut, ['retournée', 'annulé', 'payée'])) {
                foreach ($commande->contenus as $contenu) {
                    $article = produits::find($contenu->id_produit);
                    if ($article) {
                        $article->retourner_stock($contenu->quantite, $commande->shop_id);
                    }
                }
            }

            $commande->delete();
        });

        session()->flash('success', 'Commande supprimée avec succès.');
    }
}