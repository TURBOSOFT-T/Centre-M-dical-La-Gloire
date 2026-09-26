<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0 font-weight-bold text-dark">
            <i class="bx bx-vial text-primary me-2"></i> Examen : {{ $demandeExamen->examen->nom ?? 'Examen Bio' }}
            <small class="text-muted">({{ $demandeExamen->code_demande }})</small>
        </h6>
        <span class="badge {{ $demandeExamen->statut_badge ?? 'bg-secondary' }}">
            {{ ucfirst(str_replace('_', ' ', $demandeExamen->statut)) }}
        </span>
    </div>

    <div class="card-body">
        @if (session()->has('success_resultats_' . $demandeExamen->id))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success_resultats_' . $demandeExamen->id) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form wire:submit.prevent="enregistrerResultats">
            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">Analyse / Sous-examen</th>
                            <th style="width: 35%;">Valeur / Résultat</th>
                            <th style="width: 30%;">Valeurs de Référence (Normes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analysesResultats as $index => $item)
                            <tr>
                                <td class="font-weight-bold">
                                    {{ $item['nom'] ?? 'Analyse' }}
                                </td>
                                <td>
                                    <input type="text"
                                           wire:model="analysesResultats.{{ $index }}.resultat"
                                           class="form-control form-control-sm"
                                           placeholder="ex: 12.5 g/dL ou Positif">
                                </td>
                                <td>
                                    <input type="text"
                                           wire:model="analysesResultats.{{ $index }}.norme"
                                           class="form-control form-control-sm"
                                           placeholder="ex: 11.5 - 16.0">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aucune sous-analyse configurée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-8">
                    <label class="form-label font-weight-bold">Conclusion / Remarques du Biologiste</label>
                    <input type="text" wire:model="conclusion" class="form-control form-control-sm" placeholder="Avis global sur les résultats...">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Statut de l'Examen</label>
                    <select wire:model="statut" class="form-select form-select-sm">
                        <option value="prescrit">Prescrit (En attente)</option>
                        <option value="en_cours">En cours d'analyse</option>
                        <option value="termine">Terminé / Validé</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-sm btn-primary px-3 radius-30">
                    <i class="bx bx-save me-1"></i> Enregistrer les résultats
                </button>
            </div>
        </form>
    </div>
</div>