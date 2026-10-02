<div id="zone-impression">
    {{-- En-tête de la page statistique --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-clipboard2-pulse-fill text-primary me-2"></i> Statistiques & Rapports des Consultations</h4>
            <p class="text-muted mb-0">Suivi financier et volumes des consultations par période et par médecin.</p>
        </div>
        
    </div>

    {{-- CARDS STATISTIQUES GLOBALES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50 mb-1">Total Consultations</h6>
                    <h3 class="fw-bold mb-0">{{ $totalConsultations }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50 mb-1">Montant Total (Global)</h6>
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
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Code, patient...">
                    </div>
                </div>

                <div class="col-md-3">
                    <select class="form-select bg-light" wire:model.live="periode">
                        <option value="tous">Toutes les périodes</option>
                        <option value="jour">Aujourd'hui (Jour)</option>
                        <option value="semaine">Cette Semaine</option>
                        <option value="mois">Ce Mois</option>
                        <option value="intervalle">Intervalle personnalisé (Date & Heure)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-select bg-light" wire:model.live="filterStatut">
                        <option value="">Tous les statuts</option>
                        <option value="programme">Programmé</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="annule">Annulé</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-select bg-light" wire:model.live="filterMedecin">
                        <option value="">Tous les médecins</option>
                        @foreach($medecins as $med)
                            <option value="{{ $med->id }}">{{ $med->name ?? $med->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

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
                            <th>Médecin</th>
                            <th>Type</th>
                            <th>Date RDV</th>
                            <th>Total Facturé</th>
                            <th>Payé</th>
                          
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultations as $con)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $con->code_consultation }}</span></td>
                            <td>
                                @if($con->patient)
                                    {{ $con->patient->nom }} {{ $con->patient->prenom }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-dark fw-semibold">{{ $con->medecin->name ?? ($con->medecin->nom ?? 'Non assigné') }}</small>
                            </td>
                            <td><small class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $con->type)) }}</small></td>
                            <td><small>{{ $con->date_heure_rdv ? $con->date_heure_rdv->format('d/m/Y H:i') : '-' }}</small></td>
                            <td class="fw-semibold">{{ number_format($con->total_facture, 0, ',', ' ') }} FCFA</td>
                            <td class="text-success fw-semibold">{{ number_format($con->montant_paye, 0, ',', ' ') }} FCFA</td>
                           
                            <td>
                                {{-- Utilisation de l'accessor getStatutBadgeClassesAttribute & getStatutLabelAttribute --}}
                                <span class="badge {{ $con->statut_badge_classes }}">
                                    {{ $con->statut_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open display-5 d-block mb-2"></i>
                                Aucune donnée statistique pour les filtres sélectionnés.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($consultations->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $consultations->links() }}
        </div>
        @endif
    </div>

    {{-- STYLE D'IMPRESSION PROPRE SANS LE MENU --}}
    @push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #zone-impression, #zone-impression * {
                visibility: visible !important;
            }
            #zone-impression {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            .d-flex.justify-content-between.align-items-center.mb-4 div:last-child,
            .card.shadow-sm.border-0.mb-4,
            .card-footer {
                display: none !important;
            }
            .row.g-3.mb-4 {
                display: flex !important;
                flex-wrap: nowrap !important;
                gap: 10px !important;
                margin-bottom: 20px !important;
            }
            .row.g-3.mb-4 > div {
                flex: 1 !important;
                max-width: 25% !important;
            }
            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                background-color: #fff !important;
            }
            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .table th, .table td {
                border: 1px solid #333 !important;
                padding: 6px 8px !important;
                font-size: 11px !important;
                color: #000 !important;
            }
            .table thead th {
                background-color: #e9ecef !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    @endpush
</div>