<?php

namespace App\Livewire\Commandes;

use App\Http\Traits\ListGouvernorats as TraitsListGouvernorats;
use App\Models\commandes;
use App\Models\produits;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ListCommande extends Component
{
    use WithPagination;
    use TraitsListGouvernorats;

    protected $paginationTheme = 'bootstrap';

    public $selectedCommandes = [];
    public $date, $statut, $key, $commercial_id, $date_debut, $date_fin, $statut2;



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

        // Recharge les commandes après réinitialisation
        $this->filtrer();
    }



    public function filtrer()
    {
        // Cette méthode peut rester vide. 
        // Livewire l'appellera simplement pour satisfaire la requête sans erreur.
        $this->resetPage(); // Optionnel : utile pour revenir à la page 1 lors d'un clic
    }



    public function render()
    {
        $commandesQuery = commandes::query();


        $user = auth()->user();

        // Filtre par commercial (admin seulement)
        if ($user->role == 'admin' && !empty($this->commercial_id)) {
            $commandesQuery->where('commercial_id', $this->commercial_id);
        }

        if ($user->role == 'admin' && !empty($this->caisse_id)) {
            $commandesQuery->where('caisse_id', $this->caisse_id);
        }


        // $commandesQuery->where('statut', '!=', 'payée');
        // $commandesQuery->where('etat', '!=', 'annulé');
        //  $commandesQuery->where('statut', '!=', 'retournée');
        if (strlen($this->date) > 0) {
            $commandesQuery->whereDate('created_at', $this->date);
        }

        // Filtre par date + heure
        if (!empty($this->date)) {
            $dateTime = str_replace('T', ' ', $this->date);
            $commandesQuery->where('created_at', '>=', $dateTime);
        }

        // Filtre entre date + heure
        if (!empty($this->date_debut) && !empty($this->date_fin)) {
            $debut = str_replace('T', ' ', $this->date_debut);
            $fin = str_replace('T', ' ', $this->date_fin);

            $commandesQuery->whereBetween('created_at', [$debut, $fin]);
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
        $commandes = $commandesQuery->Orderby('id', "Desc")->paginate(10);
        $total = commandes::count();

        // ✅ Envoyer aussi la liste des commerciaux au view si admin
        $commerciaux = auth()->user()->role == 'admin'
            ? User::where('role', 'caisse')->get()
            : collect();

        /* 
            $commerciaux = auth()->user()->role == 'admin'
    ? User::whereNotIn('role', ['client', 'admin'])->get()
    : collect(); */
        return view('livewire.commandes.list-commande', compact("commandes", "total", "commerciaux"));
    }
    public function updateStatus($commandeId, $newStatus)
    {
        $commande = commandes::findOrFail($commandeId);
        $user = auth()->user();
        $client = $commande->user;

        // 🛑 Si la commande est confirmée et que l'utilisateur n'est ni
        // le caissier qui a confirmé, ni un admin → INTERDIT
        if (
            $commande->etat === 'confirmé'
            && $commande->caisse_id != $user->id
            && $user->role != 'admin'
        ) {
            session()->flash('warning', 'Vous ne pouvez pas modifier cette commande confirmée par un autre caissier.');
            return;
        }

        // 🛑 Si la commande n’est pas confirmée, seul le caissier qui confirme OU admin peut continuer
        if (
            $commande->etat !== 'confirmé'
            && $user->role != 'admin'
            && $commande->caisse_id != $user->id
            && $newStatus !== 'confirmé'
        ) {
            session()->flash('warning', 'Vous devez confirmer la commande avant de la modifier.');
            return;
        }

        // -----------------------
        // 🔥 Mise à jour du statut
        // -----------------------

        $commande->statut = $newStatus;

        // ➤ Gestion des commandes retournées
        if ($newStatus == "retournée") {
            foreach ($commande->contenus as $contenus) {
                $article = produits::find($contenus->id_produit);
                if ($article) {
                    $article->retourner_stock($contenus->quantite, $commande->shop_id);
                }
            }
        }

        // ➤ Commande payée
        if ($newStatus == "payée") {
            $telephoneClient = $commande->phone ?? null;

            if ($telephoneClient) {

            $nomClient = $commande->nom ?? 'Client';
            $messageTexte = "Bonjour " . $nomClient . ", votre commande #" . $commande->reference . " d'un montant de " . number_format($commande->montant_total, 0, ',', ' ') . " FCFA a ete payee avec succes. Merci pour votre achat chez SWOOT BIO !";
                  try {
                    $response = \Illuminate\Support\Facades\Http::get('https://sms.etech-keys.com/ss/api.php', [
                        'login'         => '655262413',
                        'password'      => '655262413',
                        'sender_id'     => 'ETECH KEYS',
                        'destinataire'  => $telephoneClient,
                        'message'       => $messageTexte,
                        'ext_id'        => $commande->id,
                        'programmation' => 0
                    ]);

                

                    if (!$response->successful()) {
                        \Log::error("Erreur HTTP lors de l'envoi du SMS (Commande #" . $commande->id . ") : " . $response->body());
                    }
                } catch (\Exception $e) {
                    \Log::error("Exception SMS commande #" . $commande->id . " : " . $e->getMessage());
                }
            }
        }

        // ➤ Autres statuts avec email
        if (in_array($newStatus, ["En cours livraison", "traitement", "planification"])) {
            // $this->sendOrderConfirmationMail($commande);
        }

        $commande->save();
        
        session()->flash('success', 'Le statut de la commande a bien été mis à jour.');
    }

    public function updateStatus1($commandeId, $newStatus)
    {
        $commande = commandes::findOrFail($commandeId);
        $user = auth()->user();
        $client = $commande->user;

        // 🛑 Si la commande est confirmée et que l'utilisateur n'est ni
        // le caissier qui a confirmé, ni un admin → INTERDIT
        if (
            $commande->etat === 'confirmé'
            && $commande->caisse_id != $user->id
            && $user->role != 'admin'
        ) {
            session()->flash('warning', 'Vous ne pouvez pas modifier cette commande confirmée par un autre caissier.');
            return;
        }

        // 🛑 Si la commande n’est pas confirmée, seul le caissier qui confirme OU admin peut continuer
        if (
            $commande->etat !== 'confirmé'
            && $user->role != 'admin'
            && $commande->caisse_id != $user->id
            && $newStatus !== 'confirmé'
        ) {
            session()->flash('warning', 'Vous devez confirmer la commande avant de la modifier.');
            return;
        }

        // -----------------------
        // 🔥 Mise à jour du statut
        // -----------------------

        $commande->statut = $newStatus;

        // ➤ Gestion des commandes retournées
        if ($newStatus == "retournée") {



            foreach ($commande->contenus as $contenus) {
                $article = produits::find($contenus->id_produit);
                if ($article) {
                    //  $article->retourner_stock($contenus->quantite);
                    $article->retourner_stock($contenus->quantite, $commande->shop_id);
                }
            }

            //  $this->sendOrderConfirmationMail($commande);
        }

        // ➤ Commande payée
        if ($newStatus == "payée") {
            foreach ($commande->contenus as $contenus) {
                if ($client) {
                    $client->points += $contenus->total_gain_points;
                    $client->save();
                }
            }
        }

        // ➤ Autres statuts avec email
        if (in_array($newStatus, ["En cours livraison", "traitement", "planification"])) {
            // $this->sendOrderConfirmationMail($commande);
        }

        $commande->save();
    }
    public function confirmer($id)
    {
        $commande = commandes::find($id);
        $user = auth()->user();
        if ($commande) {

            $commande->etat = "confirmé";
            $commande->caisse_id = $user->id;
            $commande->save();
            // $this->sendOrderConfirmationMail($commande);
        }
    }

    public function annuler($id)
    {
        $user = auth()->user();
        $commande = commandes::find($id);
        $client = $commande->user;
        if ($commande) {
            foreach ($commande->contenus as $contenus) {
                $article = produits::find($contenus->id_produit);
                if ($article) {
                    //  $article->retourner_stock($contenus->quantite);
                    $article->retourner_stock($contenus->quantite, $commande->shop_id);
                }
            }
            $commande->statut = "retournée";
            $commande->etat = "annulé";
            $commande->caisse_id = $user->id;

            $commande->save();
            // $this->sendOrderConfirmationMail($commande);
        }
    }



    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $commande = $this->applyShopRestriction(commandes::query())->findOrFail($id);

            // Si la commande n'était pas déjà annulée/retournée/payée, on rend le stock
            // (Note: 'payée' mis au féminin pour correspondre à vos autres statuts)
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

        session()->flash('success', 'Commande supprimée avec succès');
    }
}
