<div class="container-fluid py-4">
    <!-- Messages de confirmation et erreurs -->
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-x-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Entête -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 font-weight-bold text-primary">
                    <i class="bx bx-shield-quarter me-2"></i>Compagnies d'Assurances Santé
                </h4>
                <p class="text-muted small mb-0">Partenaires de tiers-payant du Centre Médical La Gloire</p>
            </div>
             @can('assurance_add')
            <button wire:click="openModal" class="btn btn-primary px-4 radius-30">
                <i class="bx bx-plus me-1"></i> Nouvelle Assurance
            </button>
            @endcan
        </div>
    </div>

    <!-- Barre de Recherche et Filtres -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Rechercher par code, nom ou email...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select wire:model.live="filtreStatut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="1">Actives</option>
                        <option value="0">Inactives</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de liste -->
    <div class="card border-0 shadow-sm radius-15 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Nom de l'Organisme</th>
                            <th>Taux Défaut</th>
                            <th>Contacts</th>
                            <th>Patients Couverts</th>
                            <th>Statut</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assurances as $assurance)
                        <tr>
                            <td>
                                <span class="badge bg-soft-primary text-primary font-weight-bold">{{ $assurance->code }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $assurance->nom }}</div>
                                <small class="text-muted">{{ $assurance->adresse ?? 'Adresse non renseignée' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark font-weight-bold">{{ $assurance->taux_couverture_defaut }}%</span>
                            </td>
                            <td>
                                @if($assurance->telephone)
                                <div><i class="bx bx-phone text-muted me-1"></i>{{ $assurance->telephone }}</div>
                                @endif
                                @if($assurance->email)
                                <small class="text-muted"><i class="bx bx-envelope me-1"></i>{{ $assurance->email }}</small>
                                @endif
                                @if(!$assurance->telephone && !$assurance->email)
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary radius-30 px-3">{{ $assurance->patients_count }} patient(s)</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        wire:click="toggleStatut({{ $assurance->id }})"
                                        {{ $assurance->est_actif ? 'checked' : '' }}>
                                    <label class="form-check-label small">
                                        {{ $assurance->est_actif ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </td>
                            <td class="text-end px-4">
                                <button wire:click="showDetails({{ $assurance->id }})" class="btn btn-sm btn-outline-info me-1" title="Voir les détails">
        <i class="bx bx-show"></i>
    </button>
                                 @can('assurance_edit')
                                <button wire:click="editAssurance({{ $assurance->id }})" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bx bx-edit"></i>
                                </button>
                                @endcan

                          @can('assurance_delete')
                        <button class="btn btn-sm btn-danger" onclick="toggle_confirmation({{ $assurance->id }})">
                            <i class="bx bx-trash"></i>
                        </button>
                        @endcan
                    
                        <button class="btn btn-sm btn-success d-none" type="button" id="confirmBtn{{ $assurance->id }}"
                            wire:click="delete({{ $assurance->id }})">
                            <i class="bi bi-check-circle"></i>
                            <span class="hide-tablete">
                                Confirmer
                            </span>
                        </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Aucune compagnie d'assurance enregistrée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assurances->hasPages())
        <div class="card-footer bg-white d-flex justify-content-end pt-3">
            {{ $assurances->links() }}
        </div>
        @endif
    </div>

    <!-- Modale Formulaire -->
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content radius-15 border-0">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">
                        {{ $isEditMode ? 'Modifier la Compagnie' : 'Nouvelle Compagnie d\'Assurance' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="saveAssurance">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Code / Sigle <span class="text-danger">*</span></label>
                                <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror" placeholder="Ex: CNPS, ASCOMA">
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Raison Sociale / Nom <span class="text-danger">*</span></label>
                                <input type="text" wire:model="nom" class="form-control @error('nom') is-invalid @enderror" placeholder="Ex: ASCOMA Cameroun">
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Taux Défaut (%) <span class="text-danger">*</span></label>
                                <input type="number" min="0" max="100" wire:model="taux_couverture_defaut" class="form-control @error('taux_couverture_defaut') is-invalid @enderror">
                                @error('taux_couverture_defaut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" wire:model="telephone" class="form-control" placeholder="Ex: +237 600000000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email de contact</label>
                                <input type="email" wire:model="email" class="form-control" placeholder="contact@assurance.cm">
                            </div>

                            <div class="col-6">
                                <label class="form-label">Adresse / Siège Social</label>
                                <input type="text" wire:model="adresse" class="form-control" placeholder="Ex: Akwa, Douala">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Boîte Postale (BP)</label>
                                <input type="text" wire:model="bp" class="form-control" placeholder="Ex: BP 1234 Douala">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Numéro d'Identification Unique (NIU)</label>
                                <input type="text" wire:model="niu" class="form-control" placeholder="Ex: 123456789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Numéro de Registre de Commerce (RC)</label>
                                <input type="text" wire:model="numero_rc" class="form-control" placeholder="Ex: RC123456">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="est_actif" id="modalSwitchActif">
                                    <label class="form-check-label font-weight-bold" for="modalSwitchActif">
                                        Activer cette assurance pour le tiers-payant
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeModal">Annuler</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modale de Détails -->
    @if($isDetailModalOpen && $selectedAssurance)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content radius-15 border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold text-primary">
                        <i class="bx bx-shield-quarter me-2"></i>Détails de la Compagnie : {{ $selectedAssurance->nom }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- En-tête Statut & Code -->
                        <div class="col-12 d-flex justify-content-between align-items-center bg-soft-primary p-3 radius-10">
                            <div>
                                <span class="text-muted small">Code / Sigle :</span>
                                <h5 class="mb-0 font-weight-bold text-primary">{{ $selectedAssurance->code }}</h5>
                            </div>
                            <div>
                                <span class="badge {{ $selectedAssurance->est_actif ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2 radius-30">
                                    {{ $selectedAssurance->est_actif ? 'Active (Tiers-Payant Autorisé)' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <!-- Informations Générales -->
                        <div class="col-md-6">
                            <div class="border p-3 radius-10 h-100">
                                <h6 class="font-weight-bold text-secondary mb-3"><i class="bx bx-buildings me-1"></i> Identité & Couverture</h6>
                                <p class="mb-2"><strong>Raison Sociale :</strong> {{ $selectedAssurance->nom }}</p>
                                <p class="mb-2"><strong>Taux Couverture Défaut :</strong> <span class="badge bg-info text-dark font-weight-bold fs-6">{{ $selectedAssurance->taux_couverture_defaut }}%</span></p>
                                <p class="mb-2"><strong>Patients Associés :</strong> <span class="badge bg-secondary radius-30 px-3">{{ $selectedAssurance->patients_count ?? $selectedAssurance->patients()->count() }} patient(s)</span></p>
                            </div>
                        </div>

                        <!-- Contacts -->
                        <div class="col-md-6">
                            <div class="border p-3 radius-10 h-100">
                                <h6 class="font-weight-bold text-secondary mb-3"><i class="bx bx-phone me-1"></i> Coordonnées & Contacts</h6>
                                <p class="mb-2"><strong>Téléphone :</strong> {{ $selectedAssurance->telephone ?? 'Non renseigné' }}</p>
                                <p class="mb-2"><strong>E-mail :</strong> {{ $selectedAssurance->email ?? 'Non renseigné' }}</p>
                                <p class="mb-2"><strong>Adresse :</strong> {{ $selectedAssurance->adresse ?? 'Non renseignée' }}</p>
                                <p class="mb-0"><strong>Boîte Postale :</strong> {{ $selectedAssurance->bp ?? 'Non renseignée' }}</p>
                            </div>
                        </div>

                        <!-- Informations Fiscales & Légales -->
                        <div class="col-12">
                            <div class="border p-3 radius-10 bg-light">
                                <h6 class="font-weight-bold text-secondary mb-3"><i class="bx bx-file me-1"></i> Informations Fiscales & Immatriculation</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Numéro d'Identification Unique (NIU) :</strong> {{ $selectedAssurance->niu ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Registre du Commerce (RC) :</strong> {{ $selectedAssurance->numero_rc ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" wire:click="closeDetailModal">
                        Fermer
                    </button>
                    @can('assurance_edit')
                    <button type="button" class="btn btn-primary px-4" wire:click="editAssurance({{ $selectedAssurance->id }})">
                        <i class="bx bx-edit me-1"></i> Modifier
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endif
</div>