<?php

namespace App\Livewire\Commandes;

use App\Models\Transport;
use App\Http\Traits\ListGouvernorats as TraitsListGouvernorats;
use App\Models\config;
use App\Models\contenu_commande;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditCommande extends Component
{
    use TraitsListGouvernorats;

    public $commande;
    public $gouvernoratsTunisie;
    public $nom;
    public $prenom;
    public $adresse;
    public $gouvernorat;
    public $phone;
    public $frais;
    public $commercial_id;
    public $solde = 0;
    public $caisse_id;
    public $transports = [];
    public $transport_id;
    public $showTransportForm = false;

    public function mount($commande)
    {
        $this->commande = $commande;
        $this->commercial_id = $commande->commercial_id ?? null;
        $this->caisse_id = $commande->caisse_id ?? null;

        $this->frais = $commande->frais;
        $this->nom = $commande->nom;
        $this->prenom = $commande->prenom;
        $this->adresse = $commande->adresse;
        $this->gouvernorat = $commande->gouvernorat;
        $this->phone = $commande->phone;
        $this->transports = Transport::all();
        $this->transport_id = $commande->transport_id ?? null;
    }


    public function toggleTransportForm()
    {
        $this->showTransportForm = !$this->showTransportForm;
    }


    public function openTransportForm()
    {
        $this->showTransportForm = true;
    }


    public function closeTransportForm()
    {
        $this->showTransportForm = false;
    }


    public function applyTransport()
    {
        $transport = Transport::find($this->transport_id);

        if (!$transport) {
            session()->flash('error', 'Transport invalide');
            return;
        }

        $this->commande->transport_id = $transport->id;
        $this->commande->frais = $transport->frais;
        $this->commande->save();

        $this->showTransportForm = false;
        session()->flash('success', 'Transport appliqué');
    }


    public function removeTransport()
    {
        $config = config::first();

        $this->commande->transport_id = null;
        $this->commande->frais = $this->frais ? $config->frais : null;
        $this->commande->save();

        $this->transport_id = null;
        session()->flash('success', 'Transport annulé');
    }


    public function render()
    {
        $this->gouvernoratsTunisie = $this->getListGouvernorat();
        return view('livewire.commandes.edit-commande');
    }


    public function update_user_info()
    {
        $this->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:150',
            'phone' => 'required|string|max:100',
            'frais' => 'nullable',
        ]);

        $config = config::first();

        $this->commande->nom = $this->nom;
        $this->commande->phone = $this->phone;
        $this->commande->commercial_id = $this->commercial_id ?? null;
        $this->commande->caisse_id = Auth::id();

        if ($this->transport_id) {
            $transport = Transport::find($this->transport_id);
            if ($transport) {
                $this->commande->transport_id = $transport->id;
                $this->commande->frais = $transport->frais;
            }
        } else {
            $this->commande->frais = $this->frais ? $config->frais : null;
        }

        $person = $this->commande->user ?? $this->commande->client;

        if (!$person) {
            session()->flash('error', 'Client introuvable');
            return;
        }

        DB::transaction(function () use ($person) {


            $person->save();
            $this->commande->save();
        });

        session()->flash('success', 'Commande mise à jour avec succès !');
    }



    public function updatePrice($id_contenu, $nouveauPrix)
    {
        $contenu = contenu_commande::find(intval($id_contenu));
        if (!$contenu) {
            session()->flash('warning', 'Contenu non trouvé');
            return;
        }

        $client = $contenu->commandes->user ?? $contenu->commandes->client;
        $nouveauPrix = floatval($nouveauPrix);

        if ($nouveauPrix < 0) {
            session()->flash('error', 'Le prix ne peut pas être négatif');
            return;
        }

        $ancienPrix = $contenu->prix_unitaire;
        $diffPrix = $nouveauPrix - $ancienPrix;
        $montantVariation = abs($diffPrix) * $contenu->quantite;

        DB::transaction(function () use ($contenu, $client, $diffPrix, $montantVariation, $nouveauPrix) {
            if ($diffPrix > 0) {
                if ($contenu->commandes->mode === 'solde') {
                    if ($client && $client->solde < $montantVariation) {
                        throw new \Exception('Solde insuffisant pour augmenter le prix');
                    }
                    if ($client) {
                        $client->solde -= $montantVariation;
                    }
                }

                if ($contenu->commandes->mode === 'points') {
                    if ($client && $client->points < $montantVariation) {
                        throw new \Exception('Points insuffisants pour augmenter le prix');
                    }
                    if ($client) {
                        $client->points -= $montantVariation;
                    }
                }
            } elseif ($diffPrix < 0 && $client) {
                if ($contenu->commandes->mode === 'solde') {
                    $client->solde += $montantVariation;
                }
                if ($contenu->commandes->mode === 'points') {
                    $client->points += $montantVariation;
                }
            }

            if ($client) {
                $client->save();
            }

            $contenu->prix_unitaire = $nouveauPrix;
            $contenu->save();
        });

        $this->commande->refresh();
        session()->flash('success', 'Le prix unitaire a été modifié');
    }


    public function change($id_contenu, $quantite, $type)
    {
        $contenu = contenu_commande::find(intval($id_contenu));
        if (!$contenu) {
            session()->flash('warning', 'Contenu non trouvé');
            return;
        }

        $client = $contenu->commandes->user ?? $contenu->commandes->client;
        $produit = $contenu->produit;
        $quantite = intval($quantite);

        if ($quantite <= 0) {
            session()->flash('error', 'La quantité doit être supérieure à 0');
            return;
        }

        $someQuantite = $contenu->quantite;
        $diff = $quantite - $someQuantite;
        $montantVariation = $contenu->prix_unitaire * abs($diff);

        // Récupération de l'ID du magasin lié à la commande
        $shopId = $this->commande->id_shop ?? null;

        DB::transaction(function () use ($contenu, $client, $produit, $quantite, $diff, $montantVariation, $shopId) {
            if ($diff > 0) {
                if ($contenu->commandes->mode === 'solde') {
                    if ($client && $client->solde < $montantVariation) {
                        throw new \Exception('Solde insuffisant pour augmenter la quantité');
                    }
                    if ($client) {
                        $client->solde -= $montantVariation;
                    }
                }

                if ($contenu->commandes->mode === 'points') {
                    if ($client && $client->points < $montantVariation) {
                        throw new \Exception('Le nombre de points est insuffisant');
                    }
                    if ($client) {
                        $client->points -= $montantVariation;
                    }
                }

                // Le stock est diminué pour le shop concerné
                $produit->diminuer_stock($diff, $shopId);
            } elseif ($diff < 0) {
                if ($client) {
                    if ($contenu->commandes->mode === 'solde') {
                        $client->solde += $montantVariation;
                    }
                    if ($contenu->commandes->mode === 'points') {
                        $client->points += $montantVariation;
                    }
                }
                // Le stock est retourné pour le shop concerné
                $produit->retourner_stock(abs($diff), $shopId);
            }

            if ($client) {
                $client->save();
            }

            $contenu->quantite = $quantite;
            $contenu->total_gain_points = $produit->points * $quantite;
            $contenu->save();
        });
    }

    
    public function delete($id)
    {
        $contenu = contenu_commande::find(intval($id));
        if (!$contenu) {
            session()->flash('warning', 'Contenu non trouvé');
            return;
        }


        $shopId = $this->commande->id_shop ?? null;

        DB::transaction(function () use ($contenu, $shopId) {

            // Le stock complet de la ligne est restitué au magasin concerné
            $contenu->produit->retourner_stock($contenu->quantite, $shopId);
            $contenu->delete();
        });

        session()->flash('success', 'Le contenu a été supprimé de votre commande');

        $contenusRestants = $this->commande->contenus()->count();
        if ($contenusRestants == 0) {
            $this->commande->delete();
            return redirect()->route('commandes')->with('success', 'La commande a été supprimée car elle était vide');
        }
    }
}
