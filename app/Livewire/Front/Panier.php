<?php

namespace App\Livewire\Front;

use App\Models\ProductShop;
use App\Models\produits;
use Livewire\Component;

class Panier extends Component
{
    public $total = 0;

    public function render()
    {
        $paniers_session = session('cart', []);
        $paniers = [];
        $this->total = 0;

        foreach ($paniers_session as $shop_id => $produits_data) {
            foreach ($produits_data as $id_produit => $item) {
                $produit = produits::find($id_produit);
                if ($produit) {
                    $prix = $produit->getPrice($shop_id);
                    $qte = $item['quantite'] ?? 1;

                    $paniers[] = [
                        'id_produit' => $id_produit,
                        'shop_id'    => $shop_id,
                        'nom'        => $produit->nom,
                        'photo'      => $produit->photo,
                        'quantite'   => $qte,
                        'prix'       => $prix,
                        'subtotal'   => $qte * $prix,
                    ];
                    $this->total += ($qte * $prix);
                }
            }
        }

        return view('livewire.front.panier', compact("paniers"));
    }

    
public function update($id_produit, $shop_id, $quantite)
{
    $quantite_demandee = (int)$quantite;
    
    // 1. Récupérer le stock réel disponible
    $stock = \App\Models\ProductShop::where('produit_id', $id_produit)
        ->where('shop_id', $shop_id)
        ->first();

    $stock_max = $stock ? $stock->stock_particulier : 0;

    // 2. Si la quantité demandée dépasse le stock ou est <= 0
    if ($quantite_demandee <= 0) {
        $this->delete($id_produit, $shop_id);
        return;
    }

    if ($quantite_demandee > $stock_max) {
        // BLOCAGE : On force la valeur au stock max disponible
        $panier = session('cart', []);
        if (isset($panier[$shop_id][$id_produit])) {
            $panier[$shop_id][$id_produit]['quantite'] = $stock_max;
            session(['cart' => $panier]);
        }

        // ENVOI DE L'ALERTE
        $this->dispatch('alert', [
            'type' => 'warning', 
            'message' => 'Quantité ajustée au stock maximum disponible (' . $stock_max . ')'
        ]);
        
        // On force le rafraîchissement pour que l'input affiche $stock_max
        $this->dispatch('panierUpdated');
        return;
    }

    // 3. Si la quantité est valide, on met à jour normalement
    $panier = session('cart', []);
    if (isset($panier[$shop_id][$id_produit])) {
        $panier[$shop_id][$id_produit]['quantite'] = $quantite_demandee;
        session(['cart' => $panier]);
        $this->dispatch('panierUpdated');
    }
}
    public function update2($id_produit, $shop_id, $quantite)
    {
        $quantite = (int)$quantite;

        // 1. Suppression si quantité <= 0
        if ($quantite <= 0) {
            $this->delete($id_produit, $shop_id);
            return;
        }

        // 2. Vérification stricte du stock dans ce magasin précis
        $stock = ProductShop::where('produit_id', $id_produit)
            ->where('shop_id', $shop_id)
            ->first();

        if (!$stock || $stock->stock_particulier < $quantite) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Stock insuffisant pour cette boutique ! Disponible : ' . ($stock->stock_particulier ?? 0)
            ]);
            // On ne met pas à jour la session, le composant se re-rendra 
            // automatiquement, réinitialisant l'input à la valeur correcte.
            return;
        }

        // 3. Mise à jour sécurisée de la session
        $panier = session('cart', []);
        if (isset($panier[$shop_id][$id_produit])) {
            $panier[$shop_id][$id_produit]['quantite'] = $quantite;
            session(['cart' => $panier]);

            // Notification pour rafraîchir le header (compteur/prix) via Script.js
            $this->dispatch('panierUpdated');
        }
    }/* 
public function update($id_produit, $shop_id, $quantite)
{
    $panier = session('cart', []);
    if ($quantite <= 0) {
        $this->delete($id_produit, $shop_id);
        return;
    }
    if (isset($panier[$shop_id][$id_produit])) {
        $panier[$shop_id][$id_produit]['quantite'] = (int)$quantite;
        session(['cart' => $panier]);
        $this->dispatch('panierUpdated'); // Notification pour le JS
    }
} */

    public function delete($id_produit, $shop_id)
    {
        $panier = session('cart', []);
        if (isset($panier[$shop_id][$id_produit])) {
            unset($panier[$shop_id][$id_produit]);
            if (empty($panier[$shop_id])) unset($panier[$shop_id]);
            session(['cart' => $panier]);
            $this->dispatch('panierUpdated'); // Notification pour le JS
        }
    }
    /* public function delete($id_produit, $shop_id)
    {
        $panier = session('cart', []);
        if (isset($panier[$shop_id][$id_produit])) {
            unset($panier[$shop_id][$id_produit]);
            if (empty($panier[$shop_id])) unset($panier[$shop_id]);
            session(['cart' => $panier]);

            $this->total = 0;

            session(['cart' => $panier]);
            return response()->json(["statut" => true]);
        }
    } */
}
