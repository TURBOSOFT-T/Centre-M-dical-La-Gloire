<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\commandes;
use App\Models\contenu_commande;
use App\Models\produits;
use App\Models\ProductShop;
use App\Models\Shop;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Valeurs par défaut avec date et heure (début et fin du mois courant)
        $defaultStart = Carbon::now()->startOfMonth()->format('Y-m-d\TH:i');
        $defaultEnd = Carbon::now()->endOfMonth()->format('Y-m-d\TH:i');

        $startDateTimeInput = $request->input('start_date', $defaultStart);
        $endDateTimeInput = $request->input('end_date', $defaultEnd);

        // 🔹 Validation : s'assurer que start_date est inférieur à end_date
        if (strtotime($startDateTimeInput) > strtotime($endDateTimeInput)) {
            // Si la date de début est supérieure, on réinitialise ou inverse
            $startDateTimeInput = $defaultStart;
            $endDateTimeInput = $defaultEnd;
        }

        // Formatage pour la base de données (remplacement du 'T' par un espace)
        $startDateTime = str_replace('T', ' ', $startDateTimeInput) . (strlen($startDateTimeInput) === 16 ? ':00' : '');
        $endDateTime = str_replace('T', ' ', $endDateTimeInput) . (strlen($endDateTimeInput) === 16 ? ':59' : '');

        $shopId = $request->input('shop_id');

        // 1. Récupérer toutes les boutiques et la boutique active
        $shops = Shop::all();
        $selectedShop = !empty($shopId) ? Shop::find($shopId) : null;

        // 2. Requête des commandes sur la période précise (date + heure)
        $commandesQuery = commandes::
        whereBetween('created_at', [$startDateTime, $endDateTime])
        ->where('statut', '=', 'payée');
        if (!empty($shopId)) {
            $commandesQuery->where('shop_id', $shopId);
        }

        $totalChiffreAffaires = (clone $commandesQuery)->sum('montant_total');
        $totalVentesCount = (clone $commandesQuery)->count();
        $commandesIds = $commandesQuery->pluck('id');

        // 3. Récupérer tous les produits
        $tousProduits = produits::all();

        $produitsStockFaible = collect();
        $totalProduitsStock = 0;
        $valeurStock = 0;

        // 4. Rapport détaillé par produit
        $rapportProduits = $tousProduits->map(function ($produit) use ($commandesIds, $shopId, &$produitsStockFaible, &$totalProduitsStock, &$valeurStock) {
            
            // Récupération du stock particulier via ProductShop
            if (!empty($shopId)) {
                $productShop = ProductShop::where('shop_id', $shopId)
                    ->where('produit_id', $produit->id)
                    ->first();

                $stockRestant = $productShop ? $productShop->stock_particulier : 0;
                $prixUnitaire = ($productShop && !is_null($productShop->price_particulier)) ? $productShop->price_particulier : $produit->prix;
            } else {
                $stockRestant = ProductShop::where('produit_id', $produit->id)->sum('stock_particulier');
                if ($stockRestant == 0 && isset($produit->stock)) {
                    $stockRestant = $produit->stock;
                }
                $prixUnitaire = $produit->prix;
            }

            $totalProduitsStock += $stockRestant;
            $valeurStock += ($produit->prix_achat ?? 0) * $stockRestant;

            if ($stockRestant <= 10) {
                $produitsStockFaible->push((object)[
                    'nom' => $produit->nom,
                    'stock' => $stockRestant
                ]);
            }

            // Calcul des ventes sur la période avec heure
            $contenusQuery = contenu_commande::whereIn('id_commande', $commandesIds)
                ->where('id_produit', $produit->id);

            if (!empty($shopId)) {
                $contenusQuery->where('shop_id', $shopId);
            }

            $contenusFiltres = $contenusQuery->get();
            $stockVendu = $contenusFiltres->sum('quantite');
            $montantTotal = $contenusFiltres->sum(function ($item) {
                return $item->quantite * $item->prix_unitaire;
            });

            return (object) [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'prix' => $prixUnitaire,
                'stock_restant' => $stockRestant,
                'stock_vendu' => $stockVendu,
                'montant_total' => $montantTotal,
            ];
        });

        return view('admin.statistiques.index', compact(
            'totalChiffreAffaires',
            'totalVentesCount',
            'produitsStockFaible',
            'totalProduitsStock',
            'valeurStock',
            'rapportProduits',
            'startDateTimeInput', // Remplacé pour le champ input datetime-local
            'endDateTimeInput',   // Remplacé pour le champ input datetime-local
            'shops',
            'shopId',
            'selectedShop'
        ));
    }
}