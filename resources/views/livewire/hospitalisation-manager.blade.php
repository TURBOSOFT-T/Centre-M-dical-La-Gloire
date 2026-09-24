<div>
    {{-- Notifications --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ==================== MODE INDEX ==================== --}}
    @if($mode === 'index')
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center justify-content-between">
                    {{-- Recherche textuelle --}}
                    <div class="col-md-5 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" 
                                   wire:model.live.debounce.300ms="search" 
                                   placeholder="Code, chambre, patient...">
                        </div>
                    </div>

                    {{-- Filtre par Statut --}}
                    <div class="col-md-4 col-lg-3">
                        <select class="form-select bg-light" wire:model.live="filterStatut">
                            <option value="">Tous les statuts</option>
                            <option value="en_cours">En cours</option>
                            <option value="libere">Libéré</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>

                    {{-- Bouton Nouvelle Admission --}}
                    <div class="col-auto">
                        <button wire:click="openCreate" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle-fill"></i> Nouvelle Admission
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des Hospitalisations --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Patient</th>
                                <th>Chambre / Lit</th>
                                <th>Service</th>
                                <th>Médecin</th>
                                <th>Entrée</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hospitalisations as $hosp)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $hosp->code_hospitalisation }}</span>
                                    </td>
                                    <td>
                                        @if($hosp->patient)
                                            <div class="fw-semibold">{{ $hosp->patient->nom }} {{ $hosp->patient->prenom }}</div>
                                            <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $hosp->patient->telephone ?? 'N/A' }}</small>
                                        @else
                                            <span class="text-muted">Inconnu</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            Ch. {{ $hosp->chambre_number }}
                                            @if($hosp->lit_number) / Lit {{ $hosp->lit_number }} @endif
                                        </span>
                                    </td>
                                    <td>{{ $hosp->service_department ?: '-' }}</td>
                                    <td>
                                        @if($hosp->medecin)
                                            <small class="fw-semibold">Dr. {{ $hosp->medecin->nom }} {{ $hosp->medecin->prenom }}</small>
                                        @else
                                            <span class="text-muted small">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($hosp->date_entree)->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($hosp->statut === 'en_cours')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="bi bi-hospital me-1"></i>En cours
                                            </span>
                                        @elseif($hosp->statut === 'libere')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle me-1"></i>Libéré
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                {{ ucfirst($hosp->statut) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button wire:click="openShow({{ $hosp->id }})" class="btn btn-outline-info" title="Consulter">
                                                Voir
                                            </button>
                                            @if($hosp->statut === 'en_cours')
                                                <button wire:click="openLiberer({{ $hosp->id }})" class="btn btn-outline-success" title="Enregistrer la sortie">
                                                    <i class="bi bi-box-arrow-right"></i> Sortie
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-hospital display-5 d-block mb-2"></i>
                                        Aucune hospitalisation enregistrée.
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
    @endif

    {{-- ==================== MODE CREATE (ADMISSION) ==================== --}}
    @if($mode === 'create')
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-hospital text-primary me-2"></i>
                    Nouvelle Admission en Hospitalisation
                </h5>
                <button wire:click="backToIndex" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Annuler
                </button>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="store">
                    
                    {{-- Section 1 : Sélection Patient & Dossier Médical --}}
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person-fill me-1"></i> Patient & Dossier Médical</h6>
                            
                            @if($selectedPatientName)
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person-check-fill text-success"></i></span>
                                    <input type="text" class="form-control bg-white fw-bold" value="{{ $selectedPatientName }}" readonly>
                                    <button type="button" wire:click="clearPatient" class="btn btn-outline-danger">
                                        <i class="bi bi-x-circle me-1"></i> Changer
                                    </button>
                                </div>
                            @else
                                <div class="position-relative">
                                    <input type="text" class="form-control @error('patient_id') is-invalid @enderror" 
                                           wire:model.live.debounce.300ms="searchPatient" 
                                           placeholder="Rechercher un patient par nom, prénom ou téléphone...">
                                    
                                    @if(!empty($patientsFound))
                                        <div class="list-group position-absolute w-100 shadow-sm z-3 mt-1">
                                            @foreach($patientsFound as $p)
                                                <button type="button" 
                                                        wire:click="selectPatient({{ $p->id }}, '{{ $p->nom }} {{ $p->prenom }}', {{ $p->dossierMedical->id ?? 'null' }})" 
                                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $p->nom }} {{ $p->prenom }}</strong>
                                                        <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i>{{ $p->telephone ?: 'Sans téléphone' }}</small>
                                                    </div>
                                                    <div>
                                                        @if($p->dossierMedical)
                                                            <span class="badge bg-success-subtle text-success me-2">Dossier: {{ $p->dossierMedical->code_dossier }}</span>
                                                        @else
                                                            <span class="badge bg-info-subtle text-info me-2">Création auto dossier</span>
                                                        @endif
                                                        <span class="btn btn-sm btn-primary">Choisir</span>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @error('patient_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @error('dossier_medical_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Section 2 : Emplacement & Médecin --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Service / Département</label>
                            <input type="text" class="form-control" wire:model="service_department" placeholder="Ex: Cardiologie, Maternité...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chambre N° <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('chambre_number') is-invalid @enderror" wire:model="chambre_number" placeholder="Ex: 102 B">
                            @error('chambre_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lit N°</label>
                            <input type="text" class="form-control" wire:model="lit_number" placeholder="Ex: Lit 1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Médecin Traitant</label>
                            <select class="form-select" wire:model="medecin_id">
                                <option value="">-- Sélectionner un médecin --</option>
                                @foreach($medecins as $med)
                                    <option value="{{ $med->id }}">Dr. {{ $med->nom }}  {{ $med->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date & Heure d'Entrée <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('date_entree') is-invalid @enderror" wire:model="date_entree">
                            @error('date_entree') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date Prévue de Sortie</label>
                            <input type="datetime-local" class="form-control" wire:model="date_sortie_prevue">
                        </div>
                    </div>

                    {{-- Section 3 : Motifs & Diagnostics --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Motif d'Admission <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('motif_admission') is-invalid @enderror" wire:model="motif_admission" rows="3" placeholder="Description des symptômes ou motifs..."></textarea>
                            @error('motif_admission') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Diagnostic d'Entrée</label>
                            <textarea class="form-control" wire:model="diagnostic_entree" rows="3" placeholder="Observations cliniques initiales..."></textarea>
                        </div>
                    </div>

                    {{-- Section 4 : Tarification --}}
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-cash-stack me-1"></i> Tarification Prévisionnelle</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tarif Journalier <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control @error('tarif_journalier') is-invalid @enderror" wire:model="tarif_journalier">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('tarif_journalier') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Frais Soins & Chambre</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" wire:model="frais_soins_chambre">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Couverture / Part Assurance</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" wire:model="part_assurance">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" wire:click="backToIndex" class="btn btn-light border">Annuler</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i> Enregistrer l'Admission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ==================== MODE SHOW (CONSULTATION DETAILLEE) ==================== --}}
    @if($mode === 'show' && $selectedHospitalisation)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0">
                        Hospitalisation <span class="text-primary">{{ $selectedHospitalisation->code_hospitalisation }}</span>
                    </h5>
                    <small class="text-muted">Enregistrée par : {{ $selectedHospitalisation->agent->name ?? 'Système' }}</small>
                </div>
                <div class="d-flex gap-2">
                    @if($selectedHospitalisation->statut === 'en_cours')
                        <button wire:click="openLiberer({{ $selectedHospitalisation->id }})" class="btn btn-success btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> Enregistrer Sortie
                        </button>
                    @endif
                    <button wire:click="backToIndex" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Retour
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    {{-- Patient & Emplacement --}}
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-lines-fill me-1"></i> Patient & Localisation</h6>
                            <p class="mb-2"><strong>Nom complet :</strong> {{ $selectedHospitalisation->patient->nom ?? 'N/A' }} {{ $selectedHospitalisation->patient->prenom ?? '' }}</p>
                            <p class="mb-2"><strong>Téléphone :</strong> {{ $selectedHospitalisation->patient->telephone ?? 'Non renseigné' }}</p>
                            <p class="mb-2"><strong>Dossier Médical :</strong> <span class="badge bg-secondary">{{ $selectedHospitalisation->dossierMedical->code_dossier ?? 'N/A' }}</span></p>
                            <p class="mb-2"><strong>Service :</strong> {{ $selectedHospitalisation->service_department ?: 'Non spécifié' }}</p>
                            <p class="mb-2"><strong>Chambre :</strong> {{ $selectedHospitalisation->chambre_number }} (Lit : {{ $selectedHospitalisation->lit_number ?: 'N/A' }})</p>
                            <p class="mb-0"><strong>Médecin Traitant :</strong> Dr. {{ $selectedHospitalisation->medecin->name ?? 'Non assigné' }}</p>
                        </div>
                    </div>

                    {{-- Dates & Statut --}}
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-range me-1"></i> Suivi Séjour & Statut</h6>
                            <p class="mb-2"><strong>Date d'Entrée :</strong> {{ \Carbon\Carbon::parse($selectedHospitalisation->date_entree)->format('d/m/Y à H:i') }}</p>
                            <p class="mb-2"><strong>Sortie Prévue :</strong> {{ $selectedHospitalisation->date_sortie_prevue ? \Carbon\Carbon::parse($selectedHospitalisation->date_sortie_prevue)->format('d/m/Y à H:i') : 'Non définie' }}</p>
                            <p class="mb-2"><strong>Sortie Effective :</strong> {{ $selectedHospitalisation->date_sortie_effective ? \Carbon\Carbon::parse($selectedHospitalisation->date_sortie_effective)->format('d/m/Y à H:i') : 'Toujours hospitalisé' }}</p>
                            <p class="mb-2">
                                <strong>Statut Séjour :</strong> 
                                @if($selectedHospitalisation->statut === 'en_cours')
                                    <span class="badge bg-warning text-dark">En cours</span>
                                @elseif($selectedHospitalisation->statut === 'libere')
                                    <span class="badge bg-success">Libéré</span>
                                @else
                                    <span class="badge bg-secondary">{{ $selectedHospitalisation->statut }}</span>
                                @endif
                            </p>
                            <p class="mb-0">
                                <strong>Statut Paiement :</strong> 
                                <span class="badge bg-info text-dark">{{ ucfirst($selectedHospitalisation->statut_paiement ?? 'non_paye') }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Observations médicales --}}
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-journal-medical me-1"></i> Résumé Médical</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong class="d-block text-muted">Motif d'Admission :</strong>
                                    <p class="mb-0">{{ $selectedHospitalisation->motif_admission }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong class="d-block text-muted">Diagnostic d'Entrée :</strong>
                                    <p class="mb-0">{{ $selectedHospitalisation->diagnostic_entree ?: 'Aucun' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong class="d-block text-muted">Diagnostic de Sortie :</strong>
                                    <p class="mb-0">{{ $selectedHospitalisation->diagnostic_sortie ?: 'Non renseigné' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong class="d-block text-muted">Observations / Recommandations :</strong>
                                    <p class="mb-0">{{ $selectedHospitalisation->observations ?: 'Aucune observation' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bilan Financier --}}
                    <div class="col-12">
                        <div class="border rounded p-3 bg-body-tertiary">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calculator me-1"></i> Bilan Financier</h6>
                            <div class="row text-center g-3">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Montant Total</small>
                                    <span class="fs-5 fw-bold text-dark">{{ number_format($selectedHospitalisation->montant_total ?? 0, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Part Assurance</small>
                                    <span class="fs-5 fw-bold text-primary">{{ number_format($selectedHospitalisation->part_assurance ?? 0, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Part Patient Due</small>
                                    <span class="fs-5 fw-bold text-danger">{{ number_format($selectedHospitalisation->part_patient ?? 0, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Montant Payé</small>
                                    <span class="fs-5 fw-bold text-success">{{ number_format($selectedHospitalisation->montant_paye ?? 0, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== MODE LIBERER (SORTIE PATIENT) ==================== --}}
    @if($mode === 'liberer' && $selectedHospitalisation)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 text-success">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Enregistrer la Sortie du Patient ({{ $selectedHospitalisation->code_hospitalisation }})
                </h5>
                <button wire:click="backToIndex" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Annuler
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Patient :</strong> {{ $selectedHospitalisation->patient->nom ?? '' }} {{ $selectedHospitalisation->patient->prenom ?? '' }} 
                        | <strong>Chambre :</strong> {{ $selectedHospitalisation->chambre_number }}
                        | <strong>Entré le :</strong> {{ \Carbon\Carbon::parse($selectedHospitalisation->date_entree)->format('d/m/Y H:i') }}
                    </div>
                </div>

                <form wire:submit.prevent="enregistrerSortie">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date & Heure Effective de Sortie <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('date_sortie_effective') is-invalid @enderror" wire:model="date_sortie_effective">
                            @error('date_sortie_effective') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Acompte / Montant Réglé à la Sortie</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" wire:model="montant_paye">
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Diagnostic de Sortie</label>
                            <textarea class="form-control @error('diagnostic_sortie') is-invalid @enderror" wire:model="diagnostic_sortie" rows="3" placeholder="Diagnostic final lors de la sortie..."></textarea>
                            @error('diagnostic_sortie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Observations / Consignes Post-Hospitalisation</label>
                            <textarea class="form-control" wire:model="observations" rows="3" placeholder="Traitement à domicile, repos recommandé..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" wire:click="backToIndex" class="btn btn-light border">Annuler</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-1"></i> Valider la Libération
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>