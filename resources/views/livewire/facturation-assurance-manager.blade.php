<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bx bx-shield-quarter me-2"></i> Facturation & Prise en charge Assurance</h5>
    </div>

    <div class="card-body">
        <!-- BARRE DE RECHERCHE ET FILTRES -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Rechercher patient, code consultation, N° assuré...">
                </div>
            </div>
            
            <div class="col-md-4">
                <input type="date" wire:model.live="filtreDate" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" wire:click="$set('filtreDate', '')">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- TABLEAU DES PATIENTS ASSURÉS -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Patient</th>
                        <th>Assurance & Taux</th>
                        <th>Date</th>
                        
                        <th class="text-center">Actions / Factures</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultations as $consultation)
                        @php
                            $patient = $consultation->patient;
                            $taux = $patient?->assurance?->taux ?? $patient?->taux_couverture ?? 0;
                            $totalBrut = $consultation->total_facture;
                            $partAssurance = ($totalBrut * $taux) / 100;
                            $netPatient = max(0, $totalBrut - $partAssurance);
                        @endphp
                        <tr>
                            <td><strong>{{ $consultation->code_consultation }}</strong></td>
                            <td>
                                <div class="fw-bold">{{ $patient?->nom_complet ?? $patient?->nom . ' ' . $patient?->prenom }}</div>
                                <small class="text-muted">N° Assuré: {{ $patient?->numero_assure ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $patient?->assurance?->nom ?? 'Assurance' }} ({{ $taux }}%)
                                </span>
                            </td>
                            <td>{{ $consultation->date_heure_rdv?->format('d/m/Y H:i') }}</td>
                             <td class="text-center">
                                <div class="btn-group" role="group">
                                    <!-- 1. Facture Médicaments -->
                                    <a href="{{ route('factures.medicaments', $consultation->id) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Facture de médicaments">
                                        <i class="bx bx-capsule"></i> Méd.
                                    </a>

                                    <!-- 2. Facture Examens -->
                                    <a href="{{ route('factures.examens', $consultation->id) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Facture d'examens">
                                        <i class="bx bx-test-tube"></i> Exam.
                                    </a>

                                    <!-- 3. Facture Globale / Récapitulative -->
                                    <a href="{{ route('factures.globale', $consultation->id) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-primary" 
                                       title="Facture globale récapitulative">
                                        <i class="bx bx-receipt"></i> Globale
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bx bx-folder-open display-6 d-block mb-2"></i>
                                Aucun patient assuré trouvé pour ces critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $consultations->links() }}
        </div>
    </div>
</div>