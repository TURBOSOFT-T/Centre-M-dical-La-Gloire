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
                <form method="GET" action="{{ route('admin.statistiques') }}" class="row g-2 justify-content-end">
                    <!-- Filtre Boutique -->
                    <div class="col-auto">
                        <select name="shop_id" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">-- Toutes les boutiques (Vue Globale) --</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->nom ?? $shop->name ?? 'Boutique #' . $shop->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Date et Heure Début -->
                    <div class="col-auto">
                        <input type="datetime-local" name="start_date" value="{{ $startDateTimeInput }}" class="form-control form-control-sm" title="Date et heure de début">
                    </div>
                    <!-- Date et Heure Fin -->
                    <div class="col-auto">
                        <input type="datetime-local" name="end_date" value="{{ $endDateTimeInput }}" class="form-control form-control-sm" title="Date et heure de fin">
                    </div>
                    <!-- Bouton Filtrer (Masqué à l'impression car inutile sur papier) -->
                    <div class="col-auto d-print-none">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                    </div>
                    <!-- Bouton Réinitialiser (Masqué à l'impression) -->
                    <div class="col-auto d-print-none">
                        <a href="{{ route('admin.statistiques') }}" class="btn btn-outline-secondary btn-sm" title="Réinitialiser les filtres">
                            <i class="fas fa-undo"></i> Réinitialiser
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
                    <h3 class="my-2 text-white">{{ number_format($totalChiffreAffaires, 2, ',', ' ') }} <x-devise></x-devise></h3>
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
                    <h3 class="my-2">{{ number_format($valeurStock, 2, ',', ' ') }} <x-devise></x-devise></h3>
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

        <!-- 🔹 Tableau Détaillé par Produit -->
        <div class="row">
            <div class="col-12">
                <div class="card radius-15">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Bilan détaillé par produit {{ $selectedShop ? 'pour ' . ($selectedShop->nom ?? $selectedShop->name) : '(Vue Globale)' }}</h5>
                        <!-- Le bouton d'impression ne s'imprime pas -->
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
                                        <td>{{ number_format($item->prix, 2, ',', ' ') }} <x-devise></x-devise></td>
                                        <td>
                                            <span class="badge bg-info text-dark font-14">{{ $item->stock_vendu }}</span>
                                        </td>
                                        <td class="fw-bold text-success">
                                            {{ number_format($item->montant_total, 2, ',', ' ') }} <x-devise></x-devise>
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
                                        <td colspan="5" class="text-center">Aucun produit trouvé.</td>
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
    /* 1. Masquer tous les éléments du layout global (sidebar, topbar, etc.) */
    body * {
        visibility: hidden !important;
    }

    /* 2. Rendre visible uniquement la section de contenu ciblée */
    #printable-section, #printable-section * {
        visibility: visible !important;
    }

    /* 3. Repositionner proprement la zone imprimée */
    #printable-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* 4. Masquer explicitement les filtres et boutons */
    .d-print-none {
        display: none !important;
    }

    /* 5. Désactiver les conteneurs défilants pour afficher tout le tableau */
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

    /* 🔹 6. SUPPRESSION DE L'URL ET DE LA DATE AUTOMATIQUE DU NAVIGATEUR */
    @page {
        size: auto;
        margin: 15mm; /* Ajustez la marge papier selon vos besoins */
    }
}
</style>
@endsection