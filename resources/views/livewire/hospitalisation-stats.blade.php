<div id="zone-impression">
    {{-- En-tête de la page statistique --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-bar-chart-fill text-primary me-2"></i> Statistiques & Rapports des Hospitalisations</h4>
            <p class="text-muted mb-0">Suivi financier et volumes par période, intervalle de temps et par session d'agent.</p>
        </div>

        <div>
            {{-- Bouton d'impression pour la comptabilité --}}
            <button onclick="window.print()" class="btn btn-dark d-flex align-items-center gap-2">
                <i class="bi bi-printer-fill"></i> Imprimer le Rapport Comptable
            </button>
        </div>
       
    </div>

    {{-- CARDS STATISTIQUES GLOBALES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50 mb-1">Total Admissions</h6>
                    <h3 class="fw-bold mb-0">{{ $totalAdmissions }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50 mb-1">Montant Total</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($montantTotalGlobal, 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-dark">
                <div class="card-body">
                    <h6 class="card-title text-dark-50 mb-1">Montant Encaissé</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($montantEncaisseGlobal, 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title text-dark-50 mb-1">Reste à Encaisser</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($resteAEncaisser, 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- BARRE DE FILTRES AVANCÉS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                {{-- Recherche textuelle --}}
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Code, patient...">
                    </div>
                </div>

                {{-- Filtre Période --}}
                <div class="col-md-3">
                    <select class="form-select bg-light" wire:model.live="periode">
                        <option value="tous">Toutes les périodes</option>
                        <option value="jour">Aujourd'hui (Jour)</option>
                        <option value="semaine">Cette Semaine</option>
                        <option value="mois">Ce Mois</option>
                        <option value="intervalle">Intervalle personnalisé (Date & Heure)</option>
                    </select>
                </div>

                {{-- Filtre par Statut --}}
                <div class="col-md-3">
                    <select class="form-select bg-light" wire:model.live="filterStatut">
                        <option value="">Tous les statuts</option>
                        <option value="en_cours">En cours</option>
                        <option value="libere">Libéré</option>
                        <option value="annule">Annulé</option>
                    </select>
                </div>

                {{-- Filtre par Session (Agent) --}}
                <div class="col-md-3">
                    <select class="form-select bg-light" wire:model.live="filterAgent">
                        <option value="">Toutes les sessions (Agents)</option>
                        @foreach($agents as $ag)
                            <option value="{{ $ag->id }}">{{ $ag->name ?? $ag->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SI INTERVALLE PERSONNALISÉ SÉLECTIONNÉ --}}
            @if($periode === 'intervalle')
            <div class="row g-3 mt-3 pt-3 border-top">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Date et Heure de Début :</label>
                    <input type="datetime-local" class="form-control bg-light" wire:model.live="dateDebut">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Date et Heure de Fin :</label>
                    <input type="datetime-local" class="form-control bg-light" wire:model.live="dateFin">
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- TABLEAU DE RAPPORT --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Patient</th>
                            <th>Agent (Session)</th>
                            <th>Date Entrée</th>
                            <th>Montant Total</th>
                            <th>Payé</th>
                           
                            <th>Statut Séjour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hospitalisations as $hosp)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $hosp->code_hospitalisation }}</span></td>
                            <td>
                                @if($hosp->patient)
                                    {{ $hosp->patient->nom }} {{ $hosp->patient->prenom }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted"><i class="bi bi-person-badge me-1"></i>{{ $hosp->agent->name ?? 'Système' }}</small>
                            </td>
                            <td><small>{{ $hosp->date_entree ? \Carbon\Carbon::parse($hosp->date_entree)->format('d/m/Y H:i') : '-' }}</small></td>
                            <td class="fw-semibold">{{ number_format($hosp->montant_total, 0, ',', ' ') }} FCFA</td>
                            <td class="text-success fw-semibold">{{ number_format($hosp->montant_paye, 0, ',', ' ') }} FCFA</td>
                           
                            <td>
                                <span class="badge bg-{{ $hosp->statut === 'en_cours' ? 'warning text-dark' : 'secondary' }}">
                                    {{ ucfirst($hosp->statut) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open display-5 d-block mb-2"></i>
                                Aucune donnée statistique pour les filtres sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($hospitalisations->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $hospitalisations->links() }}
        </div>
        @endif
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
</div>