<div class="container-fluid py-4">
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- En-tête -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 font-weight-bold text-primary">
                    <i class="bx bx-user-voice me-2"></i> Registre des Visiteurs
                </h4>
                <p class="text-muted small mb-0">Contrôle des entrées/sorties des familles - Centre Médical La Gloire</p>
            </div>

            <button wire:click="openModal" class="btn btn-primary px-4 radius-30">
                <i class="bx bx-plus me-1"></i> Enregistrer une Visite
            </button>
        </div>
    </div>

    <!-- Barre de Recherche -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Rechercher par patient, visiteur ou téléphone...">
            </div>
        </div>
    </div>

    <!-- Tableau du registre -->
    <div class="card border-0 shadow-sm radius-15 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Patient Visité</th>
                            <th>Visiteur (Famille)</th>
                            <th>Lien Parenté</th>
                            <th>Chambre / Lit</th>
                            <th>Entrée</th>
                            <th>Sortie</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visites as $v)
                        <tr>
                            <td><span class="badge bg-soft-primary text-primary font-weight-bold">{{ $v->code_visite }}</span></td>
                            <td>
                                <div class="font-weight-bold">{{ $v->patient->nom_complet }}</div>
                                <small class="text-muted">{{ $v->patient->code_patient }}</small>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $v->visiteur->nom_complet }}</div>
                                <small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $v->visiteur->telephone }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $v->visiteur->lien_parente ?? 'Proche' }}</span></td>
                            <td>{{ $v->chambre_lit ?? '-' }} @if($v->badge_numero) <small class="text-primary d-block">Badge N° {{ $v->badge_numero }}</small> @endif</td>
                            <td>
                                <small class="text-dark font-weight-bold">{{ $v->date_heure_entree->format('d/m/Y') }}</small>
                                <div class="small text-muted">{{ $v->date_heure_entree->format('H:i') }}</div>
                            </td>
                            <td>
                                @if($v->date_heure_sortie)
                                <small class="text-success font-weight-bold">{{ $v->date_heure_sortie->format('d/m/Y') }}</small>
                                  <div class="small text-muted">{{ $v->date_heure_sortie->format('H:i') }}</div>

                                @else
                                <span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>En cours</span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                @if(!$v->date_heure_sortie)
                                <button wire:click="marquerSortie({{ $v->id }})" class="btn btn-sm btn-outline-success" title="Enregistrer la sortie">
                                    <i class="bx bx-exit me-1"></i> Marquer Sortie
                                </button>
                                @else
                                <span class="text-muted small"><i class="bx bx-check-double text-success me-1"></i>Terminée</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Aucune visite enregistrée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($visites->hasPages())
        <div class="card-footer bg-white d-flex justify-content-end pt-3">{{ $visites->links() }}</div>
        @endif
    </div>

    <!-- MODALE ENREGISTREMENT DE VISITE -->
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content radius-15 border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold text-white"><i class="bx bx-user-plus me-1"></i> Enregistrer une nouvelle visite</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="enregistrerVisite">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                         
                            <!-- SECTION 1 : PATIENT À VISITER AVEC RECHERCHE DYNAMIQUE -->
                            <div class="col-12">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2">1. Patient à visiter</h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label font-weight-bold">Rechercher & Sélectionner le Patient <span class="text-danger">*</span></label>

                                <!-- Champ de saisie / recherche -->
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="bx bx-search-alt text-primary"></i></span>
                                    <input type="text"
                                        wire:model.live.debounce.300ms="searchPatient"
                                        class="form-control @error('patient_id') is-invalid @enderror"
                                        placeholder="Tapez le nom, prénom, code ou téléphone du patient...">
                                </div>
                                @error('patient_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                <!-- Liste dynamique des résultats sous forme de badges / cartes à cliquer -->
                                <div class="border radius-10 p-2 bg-light" style="max-height: 180px; overflow-y: auto;">
                                    <div class="list-group list-group-flush">
                                        @forelse($patients as $p)
                                        <button type="button"
                                            wire:click="selectPatient({{ $p->id }})"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center radius-8 mb-1 py-2 {{ $patient_id == $p->id ? 'active text-white bg-primary' : 'bg-white' }}">
                                            <div>
                                                <div class="font-weight-bold" style="font-size: 0.9rem;">
                                                    {{ $p->nom_complet }}
                                                </div>
                                                <small class="{{ $patient_id == $p->id ? 'text-white-50' : 'text-muted' }}">
                                                    <i class="bx bx-phone me-1"></i>{{ $p->telephone }} | Code : {{ $p->code_patient }}
                                                </small>
                                            </div>
                                            @if($patient_id == $p->id)
                                            <span class="badge bg-white text-primary font-weight-bold"><i class="bx bx-check me-1"></i>Sélectionné</span>
                                            @else
                                            <span class="badge bg-light text-dark border">Choisir</span>
                                            @endif
                                        </button>
                                        @empty
                                        <div class="text-center py-3 text-muted small">
                                            <i class="bx bx-user-x fs-4 d-block mb-1"></i>
                                            Aucun patient ne correspond à "{{ $searchPatient }}".
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>



                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2">2. Identité du Visiteur</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom & Prénom du Visiteur <span class="text-danger">*</span></label>
                                <input type="text" wire:model="nom_complet" class="form-control @error('nom_complet') is-invalid @enderror" placeholder="Ex: Jean Paul Tagne">
                                @error('nom_complet') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" wire:model="telephone" class="form-control @error('telephone') is-invalid @enderror" placeholder="Ex: 699000000">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">N° CNI / Pièce d'identité</label>
                                <input type="text" wire:model="cni_ou_piece" class="form-control" placeholder="Ex: 102938475">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lien de Parenté</label>
                                <input type="text" wire:model="lien_parente" class="form-control" placeholder="Ex: Frère, Épouse, Parent...">
                            </div>

                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2">3. Détails du séjour</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chambre / Lit</label>
                                <input type="text" wire:model="chambre_lit" class="form-control" placeholder="Ex: Chambre 12 - Lit A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">N° Badge remis</label>
                                <input type="number" wire:model="badge_numero" class="form-control" placeholder="Ex: 05">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observations</label>
                                <textarea wire:model="observations" class="form-control" rows="2" placeholder="Ex: Remise d'effets personnels..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" wire:click="closeModal">Annuler</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i>Enregistrer l'entrée</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>