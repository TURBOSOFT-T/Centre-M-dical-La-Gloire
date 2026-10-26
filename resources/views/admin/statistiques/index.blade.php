@section('titre', 'Statistiques, Stocks et Rapports')
@extends('admin.fixe')

@section('body')
<div class="page-content-wrapper">
    <!-- 🔹 Zone globale de contenu -->
    <div class="page-content" id="printable-section">

        <!-- En-tête et Filtres -->
        <div class="row mb-4 align-items-center">
            <div class="col-sm-3">
                <h4 class="mb-0">Rapport de Ventes</h4>
            </div>
            <div class="col-sm-9">
                <form method="GET" action="{{ route('admin.statistiques') }}" class="row g-2 justify-content-end align-items-end">
                    
                    <!-- Filtre Période Rapide -->
                    <!-- Filtre Période Rapide -->
<div class="col-auto">
    <label class="form-label font-12 mb-1">Période :</label>
    <select name="filtre_periode" class="form-control form-control-sm" onchange="this.form.submit()">
        <option value="toutes" {{ ($filtrePeriode ?? '') == 'toutes' ? 'selected' : '' }}>Toutes les périodes</option>
        <option value="mois" {{ ($filtrePeriode ?? '') == 'mois' ? 'selected' : '' }}>Ce Mois</option>
        <option value="jour" {{ ($filtrePeriode ?? '') == 'jour' ? 'selected' : '' }}>Aujourd'hui</option>
        <option value="semaine" {{ ($filtrePeriode ?? '') == 'semaine' ? 'selected' : '' }}>Cette Semaine</option>
        <option value="periode" {{ ($filtrePeriode ?? '') == 'periode' ? 'selected' : '' }}>Personnalisée</option>
    </select>
</div>

                    <!-- Filtre Boutique -->
                    <div class="col-auto">
                        <label class="form-label font-12 mb-1">Boutique :</label>
                        <select name="shop_id" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">-- Toutes --</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->nom ?? $shop->name ?? 'Boutique #' . $shop->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre Caisse (Évaluation) -->
                    <div class="col-auto">
                        <label class="form-label font-12 mb-1">Caisse :</label>
                        <select name="caisse_id" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">-- Toutes les caisses --</option>
                            @foreach($caisses as $caisse)
                                <option value="{{ $caisse->id }}" {{ ($caisseId ?? '') == $caisse->id ? 'selected' : '' }}>
                                    {{ $caisse->nom ?? $caisse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date et Heure Début (Affiché si Période Personnalisée) -->
                    @if(($filtrePeriode ?? '') == 'periode')
                    <div class="col-auto">
                        <label class="form-label font-12 mb-1">Du :</label>
                        <input type="datetime-local" name="start_date" value="{{ $startDateTimeInput }}" class="form-control form-control-sm">
                    </div>
                    <!-- Date et Heure Fin -->
                    <div class="col-auto">
                        <label class="form-label font-12 mb-1">Au :</label>
                        <input type="datetime-local" name="end_date" value="{{ $endDateTimeInput }}" class="form-control form-control-sm">
                    </div>
                    @endif

                    <!-- Option : Produits vendus uniquement -->
                    <div class="col-auto align-self-center mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="uniquement_vendus" value="1" id="uniquementVendusCheck" {{ !empty($uniquementVendus) ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label font-13" for="uniquementVendusCheck">
                                Vendus uniquement
                            </label>
                        </div>
                    </div>

                    <!-- Bouton Filtrer -->
                    <div class="col-auto d-print-none align-self-end">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                    </div>
                    <!-- Bouton Réinitialiser -->
                    <div class="col-auto d-print-none align-self-end">
                        <a href="{{ route('admin.statistiques') }}" class="btn btn-outline-secondary btn-sm" title="Réinitialiser">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Encart d'information du Shop sélectionné -->
        @if($selectedShop)
        <div class="alert alert-info border-0 bg-info text-dark shadow-sm mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1"><i class="fas fa-store"></i> Boutique active : {{ $selectedShop->nom ?? $selectedShop->name }}</h5>
                    <p class="mb-0 font-13">Affichage des stocks et des rapports spécifiques à ce magasin.</p>
                </div>
                <div>
                    <span class="badge bg-light text-dark font-14 px-3 py-2">ID : #{{ $selectedShop->id }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- 🔹 Cartes de Résumé (KPIs) -->
        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card radius-15 bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-0 text-white">Chiffre d'affaires</h6>
                        <h3 class="my-2 text-white">{{ number_format($totalChiffreAffaires, 0, ',', ' ') }} <x-devise></x-devise></h3>
                        <p class="mb-0 font-13 text-white-50">Période filtrée</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card radius-15 bg-success text-white shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-0 text-white">Commandes Validées</h6>
                        <h3 class="my-2 text-white">{{ $totalVentesCount }}</h3>
                        <p class="mb-0 font-13 text-white-50">Nombre de ventes</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card radius-15 bg-warning text-dark shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-0">Valeur Totale du Stock</h6>
                        <h3 class="my-2">{{ number_format($valeurStock, 0, ',', ' ') }} <x-devise></x-devise></h3>
                        <p class="mb-0 font-13">{{ $selectedShop ? 'Pour cette boutique' : 'Global' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card radius-15 bg-danger text-white shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-0 text-white">Ruptures / Stock Faible</h6>
                        <h3 class="my-2 text-white">{{ count($produitsStockFaible) }}</h3>
                        <p class="mb-0 font-13 text-white-50">Produits concernés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔹 Tableau d'évaluation des performances par Caisse -->
        @if(!empty($statsParCaisse) && $statsParCaisse->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card radius-15 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-cash-register"></i> Évaluation des Ventes par Caisse</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Caissier(ère) / Caisse</th>
                                        <th>Nombre de Ventes</th>
                                        <th>Chiffre d'Affaires Généré</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statsParCaisse as $stat)
                                    <tr>
                                        <td class="fw-bold">{{ $stat->caissier->nom ?? $stat->caissier->name ?? 'Caisse #' . $stat->caisse_id }}</td>
                                        <td><span class="badge bg-secondary">{{ $stat->total_ventes }}</span></td>
                                        <td class="fw-bold text-success">{{ number_format($stat->total_ca, 0, ',', ' ') }} <x-devise></x-devise></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- 🔹 Tableau Détaillé par Produit -->
        <div class="row">
            <div class="col-12">
                <div class="card radius-15">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Bilan détaillé par produit {{ $selectedShop ? 'pour ' . ($selectedShop->nom ?? $selectedShop->name) : '(Vue Globale)' }}</h5>
                        <button onclick="window.print()" class="btn btn-sm btn-secondary d-print-none"><i class="fas fa-print"></i> Imprimer le Bilan</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Produit</th>
                                        <th>Prix Unitaire</th>
                                        <th>Stock Vendu (Qté)</th>
                                        <th>Montant Total Ventes</th>
                                        <th>Stock Restant {{ $selectedShop ? 'dans ce Shop' : 'Global' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rapportProduits as $item)
                                    <tr>
                                        <td class="fw-bold">{{ $item->nom }}</td>
                                        <td>{{ number_format($item->prix, 0, ',', ' ') }} <x-devise></x-devise></td>
                                        <td>
                                            <span class="badge bg-info text-dark font-14">{{ $item->stock_vendu }}</span>
                                        </td>
                                        <td class="fw-bold text-success">
                                            {{ number_format($item->montant_total, 0, ',', ' ') }} <x-devise></x-devise>
                                        </td>
                                        <td>
                                            @if($item->stock_restant <= 5)
                                                <span class="badge bg-danger">{{ $item->stock_restant }} (Critique)</span>
                                            @else
                                                <span class="badge bg-success">{{ $item->stock_restant }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">Aucun produit trouvé pour les critères sélectionnés.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- 🔹 Style CSS dédié à l'impression / PDF -->
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #printable-section, #printable-section * {
        visibility: visible !important;
    }
    #printable-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .d-print-none {
        display: none !important;
    }
    .table-responsive {
        overflow: visible !important;
        width: 100% !important;
    }
    table {
        page-break-inside: auto !important;
    }
    tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    @page {
        size: auto;
        margin: 15mm;
    }
}
</style>
@endsection