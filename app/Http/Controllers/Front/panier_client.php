<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

use App\Models\produits;
use App\Models\configs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class panier_client extends Controller
{
    public function count_panier()
    {
        $panier_temporaire = session('cart', []);
        $total = 0;
        $montant_total = 0;
        $produits_list = [];

        // Vérification de sécurité : si ce n'est pas un tableau, on arrête ici
        if (!is_array($panier_temporaire)) {
            return response()->json(["total" => 0, "html" => "<li>Panier vide</li>", "montant_total" => 0]);
        }

        foreach ($panier_temporaire as $shop_id => $produits_dans_shop) {
            // Sécurité : si $produits_dans_shop n'est pas un tableau, on ignore
            if (!is_array($produits_dans_shop)) continue;

            foreach ($produits_dans_shop as $id_produit => $data) {
                $produit = produits::select('id', 'photo', 'prix', 'nom')->find($id_produit);

                if ($produit) {
                    $qte = $data['quantite'] ?? 0;

                    // Appel du prix avec le shop_id pour TODJOM MARKET
                    $prix = $produit->getPrice($shop_id);

                    $produits_list[] = [
                        'id_produit' => $produit->id,
                        'shop_id'    => $shop_id, // INDISPENSABLE pour vos boutons supprimer
                        'nom'        => $produit->nom,
                        'photo'      => Storage::url($produit->photo),
                        'quantite'   => $qte,
                        'prix'       => $prix,
                        'total'      => $qte * $prix,
                    ];

                    $montant_total += ($qte * $prix);
                    $total += $qte;
                }
            }
        }

        // Génération du HTML
        $Html = view('components.cart', ['produits' => $produits_list])->render();

        return response()->json([
            "total" => $total,
            "html" => $Html,
            "montant_total" => number_format($montant_total, 0, ',', ' ')
        ]);
    }
    public function updateQuantity(Request $request)
    {
        $panier = session('cart', []);
        $id_produit = $request->id_produit;
        $shop_id = $request->shop_id; // DOIT être envoyé par le JS

        // Accès direct à la structure [shop_id][id_produit]
        if (isset($panier[$shop_id][$id_produit])) {
            $panier[$shop_id][$id_produit]['quantite'] = max(1, intval($request->quantite));
            session(['cart' => $panier]);
        }

        return $this->count_panier();
    }




    public function cart()
    {
        //  $configs = configs::first();
        return view('front.cart.cart');
    }

    public function add(Request $request)
    {
        $id_produit = $request->input('id_produit');
        $quantite_demandee = (int)$request->input('quantite', 1);

        $produit = produits::with('stocks')->find($id_produit);
        if (!$produit || $produit->stocks->isEmpty()) {
            return response()->json(['statut' => false, 'message' => 'Produit indisponible'], 404);
        }

        $panier = session('cart', []);

        // 1. Calculer ce qui est DÉJÀ dans le panier pour ce produit
        $quantite_deja_au_panier = 0;
        foreach ($panier as $shop_items) {
            if (isset($shop_items[$id_produit])) {
                $quantite_deja_au_panier += $shop_items[$id_produit]['quantite'];
            }
        }

        // 2. Vérifier le stock global total disponible
        $stock_total_disponible = $produit->stocks->sum('stock_particulier');
        if (($quantite_deja_au_panier + $quantite_demandee) > $stock_total_disponible) {
            return response()->json([
                'statut' => false,
                'message' => "Quantité insuffisante en stock !!!!",
            ]);
        }

        // 3. Répartition intelligente (on repart sur une base propre pour la nouvelle quantité)
        $quantite_restante = $quantite_demandee;

        foreach ($produit->stocks as $stock) {
            if ($quantite_restante <= 0) break;
            if ($stock->stock_particulier <= 0) continue;

            $shop_id = $stock->shop_id;

            // On ne prend que ce qui est disponible dans ce shop précis
            $a_prendre = min($stock->stock_particulier, $quantite_restante);

            if (!isset($panier[$shop_id])) $panier[$shop_id] = [];

            if (!isset($panier[$shop_id][$id_produit])) {
                $panier[$shop_id][$id_produit] = ['id_produit' => $id_produit, 'quantite' => 0];
            }

            $panier[$shop_id][$id_produit]['quantite'] += $a_prendre;
            $quantite_restante -= $a_prendre;
        }

        session(['cart' => $panier]);
        return response()->json(['statut' => true, 'message' => ' Produit ajouté au panier !']);
    }
   

    // Suppression propre
    public function delete_produit(Request $request)
    {
        $id_produit = $request->input('id_produit');
        $shop_id = $request->input('shop_id'); // Récupération du shop_id envoyé par JS

        $panier = session('cart', []);

        // Vérifier si le produit existe dans ce magasin précis
        if (isset($panier[$shop_id][$id_produit])) {
            unset($panier[$shop_id][$id_produit]);

            // Nettoyage : si le magasin est vide, on supprime la clé du magasin
            if (empty($panier[$shop_id])) {
                unset($panier[$shop_id]);
            }

            session(['cart' => $panier]);
            return response()->json(["statut" => true]);
        }

        return response()->json(["statut" => false, "message" => "Produit non trouvé"], 404);
    }

    public function update($id_produit, $quantite)
    {
        // Find the product in the session cart and update the quantity
        $cart = session()->get('cart', []);

        foreach ($cart as &$item) {
            if ($item['id_produit'] == $id_produit) {
                $item['quantite'] = $quantite;
                break;
            }
        }

        // Save the updated cart back to session
        session(['cart' => $cart]);

        // Optionally, re-render the component or update the cart total
        $this->emit('cartUpdated');
    }
}
