<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\commandes;
use App\Models\contenu_commande;
use App\Models\produits;
use App\Models\ProductShop;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 1. Récupération des filtres
        $filtrePeriode = $request->input('filtre_periode', 'mois'); // Par défaut 'mois'
        $caisseId = $request->input('caisse_id');
        $shopId = $request->input('shop_id');
        $uniquementVendus = $request->has('uniquement_vendus');

        // 🔹 2. Gestion des dates selon le filtre de période choisi
        $now = Carbon::now();
        $defaultStart = $now->copy()->startOfMonth()->format('Y-m-d\TH:i');
        $defaultEnd = $now->copy()->endOfMonth()->format('Y-m-d\TH:i');

        $startDateTimeInput = $request->input('start_date', $defaultStart);
        $endDateTimeInput = $request->input('end_date', $defaultEnd);

        // 4. Requête de base des commandes payées
        $commandesQuery = commandes::where('statut', '=', 'payée');

        switch ($filtrePeriode) {
            case 'jour':
                $startDateTime = $now->copy()->startOfDay()->format('Y-m-d H:i:s');
                $endDateTime = $now->copy()->endOfDay()->format('Y-m-d H:i:s');
                $commandesQuery->whereBetween('created_at', [$startDateTime, $endDateTime]);
                break;
            case 'semaine':
                $startDateTime = $now->copy()->startOfWeek()->format('Y-m-d H:i:s');
                $endDateTime = $now->copy()->endOfWeek()->format('Y-m-d H:i:s');
                $commandesQuery->whereBetween('created_at', [$startDateTime, $endDateTime]);
                break;
            case 'mois':
                $startDateTime = $now->copy()->startOfMonth()->format('Y-m-d H:i:s');
                $endDateTime = $now->copy()->endOfMonth()->format('Y-m-d H:i:s');
                $commandesQuery->whereBetween('created_at', [$startDateTime, $endDateTime]);
                break;
            case 'periode':
                if (strtotime($startDateTimeInput) > strtotime($endDateTimeInput)) {
                    $startDateTimeInput = $defaultStart;
                    $endDateTimeInput = $defaultEnd;
                }
                $startDateTime = str_replace('T', ' ', $startDateTimeInput) . (strlen($startDateTimeInput) === 16 ? ':00' : '');
                $endDateTime = str_replace('T', ' ', $endDateTimeInput) . (strlen($endDateTimeInput) === 16 ? ':59' : '');
                $commandesQuery->whereBetween('created_at', [$startDateTime, $endDateTime]);
                break;
            case 'toutes':
            default:
                // Aucune restriction de date (Toutes les périodes)
                break;
        }

        if (!empty($shopId)) {
            $commandesQuery->where('shop_id', $shopId);
        }

        // Référentiels (Boutiques et Caissiers)
        $shops = Shop::all();
        $selectedShop = !empty($shopId) ? Shop::find($shopId) : null;
        $caisses = User::where('role', 'caisse')->get();

        // Statistiques globales pour l'évaluation par caisse
        $statsParCaisseQuery = clone $commandesQuery;
        $statsParCaisse = $statsParCaisseQuery
            ->select('caisse_id', DB::raw('SUM(montant_total) as total_ca'), DB::raw('COUNT(*) as total_ventes'))
            ->groupBy('caisse_id')
            ->with('caissier')
            ->get();

        // Application du filtre de caisse spécifique si demandé
        if (!empty($caisseId)) {
            $commandesQuery->where('caisse_id', $caisseId);
        }

        $totalChiffreAffaires = (clone $commandesQuery)->sum('montant_total');
        $totalVentesCount = (clone $commandesQuery)->count();
        $commandesIds = $commandesQuery->pluck('id');

        // Récupération des produits et calcul du rapport détaillé
        $tousProduits = produits::all();
        $produitsStockFaible = collect();
        $totalProduitsStock = 0;
        $valeurStock = 0;

        $rapportProduits = $tousProduits->map(function ($produit) use ($commandesIds, $shopId, &$produitsStockFaible, &$totalProduitsStock, &$valeurStock) {
            
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

            // Calcul des ventes sur la période sélectionnée
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

        // Option : Afficher uniquement les produits vendus
        if ($uniquementVendus) {
            $rapportProduits = $rapportProduits->where('stock_vendu', '>', 0);
        }

        return view('admin.statistiques.index', compact(
            'totalChiffreAffaires',
            'totalVentesCount',
            'produitsStockFaible',
            'totalProduitsStock',
            'valeurStock',
            'rapportProduits',
            'startDateTimeInput',
            'endDateTimeInput',
            'shops',
            'shopId',
            'selectedShop',
            'caisses',
            'caisseId',
            'filtrePeriode',
            'uniquementVendus',
            'statsParCaisse'
        ));
    }
}