<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 font-weight-bold">
            <i class="bx bx-test-tube me-2"></i>
            {{ $isEditing ? 'Modifier la prescription d\'examen' : 'Prescription d\'Examens & Analyses' }}
        </h6>
        @if($isEditing)
            <span class="badge bg-warning text-dark">Mode Édition</span>
        @endif
    </div>

    <div class="card-body">
        @if (session()->has('success_examen'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success_examen') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error_examen'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error me-1"></i> {{ session('error_examen') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form wire:submit.prevent="enregistrer">
            <div class="row g-3">
                {{-- Choix de l'examen --}}
                <div class="col-md-6">
                    <label class="form-label font-weight-bold">1. Sélectionner l'examen <span class="text-danger">*</span></label>
                    <select wire:model.live="examen_id" class="form-select @error('examen_id') is-invalid @enderror" @if($isEditing) disabled @endif>
                        <option value="">-- Choisir un examen du catalogue --</option>
                        @foreach($examensCatalogue as $ex)
                            <option value="{{ $ex->id }}">{{ $ex->nom }}</option>
                        @endforeach
                    </select>
                    @error('examen_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Indication médicale --}}
                <div class="col-md-6">
                    <label class="form-label font-weight-bold">Indication / Justification médicale</label>
                    <input type="text" wire:model="indication_medicale" class="form-control" placeholder="ex: Bilan pré-opératoire, suspicion d'anémie...">
                </div>
            </div>

            {{-- Sélection des sous-analyses --}}
            @if(!empty($caracteristiquesDisponibles))
                <div class="mt-4 p-3 border rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold mb-0 text-dark">
                            <i class="bx bx-list-check me-1"></i> 2. Cocher les sous-analyses requises
                        </h6>
                        <div>
                            <button type="button" wire:click="toggleToutCocher(true)" class="btn btn-sm btn-outline-primary me-1 radius-30">
                                Tout cocher
                            </button>
                            <button type="button" wire:click="toggleToutCocher(false)" class="btn btn-sm btn-outline-secondary radius-30">
                                Tout décocher
                            </button>
                        </div>
                    </div>

                    @error('analysesSelectionnees')
                        <div class="text-danger small mb-2 font-weight-bold">{{ $message }}</div>
                    @enderror

                    <div class="row g-2">
                        @foreach($caracteristiquesDisponibles as $index => $item)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               value="{{ $index }}" 
                                               id="check_analyse_{{ $index }}"
                                               wire:model.live="analysesSelectionnees">
                                        <label class="form-check-label d-flex justify-content-between align-items-center w-100 cursor-pointer" for="check_analyse_{{ $index }}">
                                            <span class="font-weight-bold text-dark">{{ $item['nom'] ?? 'Sous-analyse' }}</span>
                                            <span class="badge bg-soft-primary text-primary border">
                                                {{ number_format($item['prix'] ?? 0, 0, ',', ' ') }} FCFA
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Récapitulatif Tarifaire --}}
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                        <div>
                            <span class="text-muted me-2">Analyses cochées :</span>
                            <span class="badge bg-info text-dark">{{ count($analysesSelectionnees) }} / {{ count($caracteristiquesDisponibles) }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fs-6 text-muted me-2">Montant estimé :</span>
                            <span class="fs-5 font-weight-bold text-success">{{ number_format($tarifTotal, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Boutons d'action --}}
            <div class="text-end mt-3">
                @if($isEditing)
                    <button type="button" wire:click="annulerEdition" class="btn btn-secondary px-3 me-2 radius-30">
                        <i class="bx bx-x me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-warning px-4 radius-30">
                        <i class="bx bx-check me-1"></i> Mettre à jour la prescription
                    </button>
                @else
                    <button type="submit" class="btn btn-primary px-4 radius-30" @if(empty($caracteristiquesDisponibles)) disabled @endif>
                        <i class="bx bx-plus me-1"></i> Prescrire l'examen
                    </button>
                @endif
            </div>
        </form>

        {{-- Tableau des examens déjà prescrits avec Boutons Éditer / Supprimer --}}
        @if($examensPrescrits->count() > 0)
            <div class="mt-4 pt-3 border-top">
                <h6 class="font-weight-bold text-dark mb-3">
                    <i class="bx bx-history me-1"></i> Examens prescrits durant cette consultation ({{ $examensPrescrits->count() }})
                </h6>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Examen</th>
                                <th>Sous-analyses demandées</th>
                                <th>Indications</th>
                                <th>Tarif</th>
                                <th>Statut</th>
                                <th class="text-end px-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examensPrescrits as $demande)
                                <tr>
                                    <td class="font-weight-bold text-primary">{{ $demande->code_demande }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $demande->examen->nom ?? 'N/A' }}</div>
                                        <small class="text-muted">Par Dr. {{ $demande->prescripteur->name ?? $demande->prescripteur->nom ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if(is_array($demande->analyses_demandees))
                                            <ul class="mb-0 ps-3 small">
                                                @foreach($demande->analyses_demandees as $sub)
                                                    <li>{{ $sub['nom'] }} <span class="text-muted">({{ number_format($sub['prix'] ?? 0, 0, ',', ' ') }} FCFA)</span></li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-wrap">{{ $demande->indication_medicale ?: '-' }}</small>
                                    </td>
                                    <td class="font-weight-bold text-dark">
                                        {{ number_format($demande->tarif_brut, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        <span class="badge {{ $demande->statut_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $demande->statut)) }}
                                        </span>
                                    </td>
                                    <td class="text-end px-3">
                                        {{-- Éditer --}}
                                        <button wire:click="editerExamen({{ $demande->id }})" 
                                                class="btn btn-sm btn-outline-primary me-1 radius-8" 
                                                title="Modifier les sous-analyses ou l'indication">
                                            <i class="bx bx-edit"></i>
                                        </button>

                                        {{-- Supprimer --}}
                                        <button wire:click="supprimerExamen({{ $demande->id }})" 
                                                wire:confirm="Êtes-vous sûr de vouloir supprimer cet examen prescrit ?" 
                                                class="btn btn-sm btn-outline-danger radius-8" 
                                                title="Retirer cet examen">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>