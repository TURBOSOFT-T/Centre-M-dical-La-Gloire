<div>
    {{-- Alerts & Notifications --}}
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
                <div class="col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Rechercher par N° dossier, nom, prénom ou téléphone...">
                    </div>
                </div>

                {{-- Bouton Créer Dossier --}}
                <div class="col-auto">
                    <button wire:click="openCreate" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-folder-plus"></i> Nouveau Dossier Médical
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des Dossiers Médicaux --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code Dossier</th>
                            <th>Patient</th>
                            <th>Groupe Sanguin</th>
                            <th>Allergies</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dossiers as $dossier)
                        <tr>
                            <td>
                                <span class="fw-bold text-primary">{{ $dossier->code_dossier }}</span>
                            </td>
                            <td>
                                @if($dossier->patient)
                                <div class="fw-semibold">{{ $dossier->patient->nom }} {{ $dossier->patient->prenom }}</div>
                                <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $dossier->patient->telephone ?? 'N/A' }}</small>
                                @else
                                <span class="text-muted">Patient non associé</span>
                                @endif
                            </td>
                            <td>
                                @if($dossier->groupe_sanguin)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                                    <i class="bi bi-droplet-fill me-1"></i>{{ $dossier->groupe_sanguin }}
                                </span>
                                @else
                                <span class="text-muted small">Non renseigné</span>
                                @endif
                            </td>
                            <td>
                                @if($dossier->allergies)
                                <span class="text-truncate d-inline-block" style="max-width: 180px;" title="{{ $dossier->allergies }}">
                                    <i class="bi bi-exclamation-triangle text-warning me-1"></i>{{ $dossier->allergies }}
                                </span>
                                @else
                                <span class="text-muted small">Aucune</span>
                                @endif
                            </td>
                            <td>
                                @if($dossier->statut === 'actif')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                                @elseif($dossier->statut === 'archive')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Archivé</span>
                                @elseif($dossier->statut === 'decede')
                                <span class="badge bg-dark text-white">Décédé</span>
                                @else
                                <span class="badge bg-light text-dark">{{ ucfirst($dossier->statut) }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $dossier->created_at ? $dossier->created_at->format('d/m/Y') : '-' }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button wire:click="openShow({{ $dossier->id }})" class="btn btn-outline-info" title="Consulter le dossier">
                                   Détails
                                    </button>
                                     @if ($dossier->modifiable())
                                    <button wire:click="openEdit({{ $dossier->id }})" class="btn btn-outline-primary" title="Modifier">
                                      Modifier
                                    </button>

                                    @endif
                                    <button wire:click="delete({{ $dossier->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce dossier médical ?"
                                        class="btn btn-outline-danger" title="Supprimer">
                                      Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x display-5 d-block mb-2"></i>
                                Aucun dossier médical trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($dossiers->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $dossiers->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ==================== MODE CREATE & EDIT ==================== --}}
    @if(in_array($mode, ['create', 'edit']))
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="bi bi-journal-medical text-primary me-2"></i>
                {{ $mode === 'create' ? 'Création d\'un Dossier Médical' : 'Modification du Dossier Médical' }}
            </h5>
            <button wire:click="backToIndex" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Annuler
            </button>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="{{ $mode === 'create' ? 'store' : 'update' }}">

                {{-- Section Patient --}}
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person-fill me-1"></i> Patient Associé</h6>

                        @if($selectedPatientName)
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-person-check-fill text-success"></i></span>
                            <input type="text" class="form-control bg-white fw-bold" value="{{ $selectedPatientName }}" readonly>
                            @if($mode === 'create')
                            <button type="button" wire:click="clearPatient" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i> Changer
                            </button>
                            @endif
                        </div>
                        @else
                        <div class="position-relative">
                            <input type="text" class="form-control @error('patient_id') is-invalid @enderror"
                                wire:model.live.debounce.300ms="searchPatient"
                                placeholder="Rechercher un patient sans dossier par nom, prénom ou téléphone...">

                            @if(!empty($patientsFound))
                            <div class="list-group position-absolute w-100 shadow-sm z-3 mt-1">
                                @foreach($patientsFound as $p)
                                <button type="button"
                                    wire:click="selectPatient({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $p->nom }} {{ $p->prenom }}</strong>
                                        <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i>{{ $p->telephone ?: 'Sans téléphone' }}</small>
                                    </div>
                                    <span class="btn btn-sm btn-primary">Sélectionner</span>
                                </button>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif
                        @error('patient_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Section Informations Médicales Générale --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Groupe Sanguin</label>
                        <select class="form-select @error('groupe_sanguin') is-invalid @enderror" wire:model="groupe_sanguin">
                            <option value="">-- Non renseigné --</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        @error('groupe_sanguin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Statut du Dossier <span class="text-danger">*</span></label>
                        <select class="form-select @error('statut') is-invalid @enderror" wire:model="statut">
                            <option value="actif">Actif</option>
                            <option value="archive">Archivé</option>
                            <option value="decede">Décédé</option>
                        </select>
                        @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Antécédents & Traitements --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Antécédents Médicaux</label>
                        <textarea class="form-control" wire:model="antecedents_medicaux" rows="3" placeholder="Pathologies chroniques, diabète, hypertension..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Antécédents Chirurgicaux</label>
                        <textarea class="form-control" wire:model="antecedents_chirurgicaux" rows="3" placeholder="Opérations subies, dates..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Allergies Connues</label>
                        <textarea class="form-control" wire:model="allergies" rows="3" placeholder="Médicamenteuses, alimentaires, latex..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Traitements Chroniques en Cours</label>
                        <textarea class="form-control" wire:model="traitements_chroniques" rows="3" placeholder="Posologie, traitements au long cours..."></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" wire:click="backToIndex" class="btn btn-light border">Annuler</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> {{ $mode === 'create' ? 'Enregistrer le dossier' : 'Mettre à jour' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ==================== MODE SHOW (CONSULTATION) ==================== --}}
    @if($mode === 'show' && $selectedDossier)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <h5 class="card-title mb-0">
                    Dossier Médical : <span class="text-primary">{{ $selectedDossier->code_dossier }}</span>
                </h5>
                @if($selectedDossier->statut === 'actif')
                <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                @elseif($selectedDossier->statut === 'archive')
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Archivé</span>
                @else
                <span class="badge bg-dark text-white">{{ ucfirst($selectedDossier->statut) }}</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <button wire:click="openEdit({{ $selectedDossier->id }})" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Modifier
                </button>
                <button wire:click="backToIndex" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </button>
            </div>
        </div>

        {{-- Bannière Récapitulative Patient --}}
        <div class="card-body bg-light border-bottom">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $selectedDossier->patient->nom ?? 'Patient' }} {{ $selectedDossier->patient->prenom ?? '' }}</h6>
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $selectedDossier->patient->telephone ?? 'Non renseigné' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Groupe Sanguin</small>
                    <span class="fw-bold text-danger">
                        <i class="bi bi-droplet-fill me-1"></i>{{ $selectedDossier->groupe_sanguin ?: 'Non spécifié' }}
                    </span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Assurance</small>
                    <span class="fw-semibold text-dark">
                        {{ $selectedDossier->patient->assurance->nom ?? 'Aucune / Privé' }}
                    </span>
                </div>
                <div class="col-md-2 text-md-end">
                    <small class="text-muted d-block">Créé le</small>
                    <span class="small fw-semibold">{{ $selectedDossier->created_at ? $selectedDossier->created_at->format('d/m/Y') : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Navigation par Onglets --}}
        <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'apercu' ? 'active fw-bold' : '' }}" wire:click="switchTab('apercu')">
                        <i class="bi bi-file-earmark-medical me-1"></i> Aperçu
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'consultations' ? 'active fw-bold' : '' }}" wire:click="switchTab('consultations')">
                        <i class="bi bi-stethoscope me-1"></i> Consultations
                        <span class="badge bg-primary ms-1">{{ $selectedDossier->consultations->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'hospitalisations' ? 'active fw-bold' : '' }}" wire:click="switchTab('hospitalisations')">
                        <i class="bi bi-hospital me-1"></i> Hospitalisations
                        <span class="badge bg-secondary ms-1">{{ $selectedDossier->hospitalisations->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'rendezvous' ? 'active fw-bold' : '' }}" wire:click="switchTab('rendezvous')">
                        <i class="bi bi-calendar-event me-1"></i> Rendez-vous
                        <span class="badge bg-info text-dark ms-1">{{ $selectedDossier->rendezVous->count() }}</span>
                    </button>
                </li>

                 <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'examens' ? 'active fw-bold' : '' }}" wire:click="switchTab('rendezvous')">
                        <i class="bi bi-calendar-event me-1"></i> Examens
                        <span class="badge bg-info text-dark ms-1"></span>
                    </button>
                </li>
            </ul>
        </div>

        {{-- Contenu des Onglets --}}
        <div class="card-body">

            {{-- TAB 1: APERÇU --}}
            @if($activeTab === 'apercu')
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 bg-body-tertiary">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-heart-pulse me-1"></i> Antécédents Médicaux</h6>
                        <p class="mb-0 text-dark">{{ $selectedDossier->antecedents_medicaux ?: 'Aucun antécédent médical renseigné.' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 bg-body-tertiary">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-bandaid me-1"></i> Antécédents Chirurgicaux</h6>
                        <p class="mb-0 text-dark">{{ $selectedDossier->antecedents_chirurgicaux ?: 'Aucun antécédent chirurgical renseigné.' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 bg-body-tertiary">
                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle me-1"></i> Allergies Connues</h6>
                        <p class="mb-0 text-dark">{{ $selectedDossier->allergies ?: 'Aucune allergie signalée.' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 bg-body-tertiary">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-capsule me-1"></i> Traitements Chroniques</h6>
                        <p class="mb-0 text-dark">{{ $selectedDossier->traitements_chroniques ?: 'Aucun traitement chronique enregistré.' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- TAB 2: CONSULTATIONS DU PATIENT --}}
            @if($activeTab === 'consultations')
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-primary mb-0"><i class="bi bi-stethoscope me-1"></i> Historique des Consultations</h6>
                <span class="badge bg-light text-dark border">Total : {{ $selectedDossier->consultations->count() }}</span>
            </div>

            

            @forelse($selectedDossier->consultations as $c)
            <div class="card mb-3 border shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold text-dark"><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($c->date_consultation ?? $c->created_at)->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div>
                        <span class="badge bg-primary">
                            <i class="bi bi-person-badge me-1"></i> Dr. {{ $c->medecin->nom ?? $c->medecin->name ?? 'Non spécifié' }} {{ $c->medecin->prenom ?? '' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong class="text-secondary d-block mb-1"><i class="bi bi-chat-left-text me-1"></i> Motif de consultation :</strong>
                            <p class="mb-0 text-dark bg-light p-2 rounded border-start border-3 border-primary">{{ $c->motif ?: 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong class="text-secondary d-block mb-1"><i class="bi bi-clipboard2-pulse me-1"></i> Diagnostic :</strong>
                            <p class="mb-0 text-dark bg-light p-2 rounded border-start border-3 border-success">{{ $c->diagnostic ?: 'Non renseigné' }}</p>
                        </div>

                        @if(!empty($c->observation) || !empty($c->symptomes))
                        <div class="col-md-12">
                            <strong class="text-secondary d-block mb-1"><i class="bi bi-search me-1"></i> Observations / Symptômes :</strong>
                            <p class="mb-0 text-dark small">{{ $c->observation ?? $c->symptomes }}</p>
                        </div>
                        @endif

                        @if(!empty($c->ordonnance) || !empty($c->traitement))
                        <div class="col-md-12">
                            <strong class="text-secondary d-block mb-1"><i class="bi bi-capsule me-1"></i> Ordonnance / Traitement prescrit :</strong>
                            <div class="p-2 bg-warning-subtle text-dark rounded border border-warning-subtle small">
                                {{ $c->ordonnance ?? $c->traitement }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted border rounded bg-light">
                <i class="bi bi-file-earmark-medical display-6 d-block mb-2"></i>
                Aucune consultation enregistrée dans ce dossier médical.
            </div>
            @endforelse
            @endif

            {{-- TAB 3: HOSPITALISATIONS --}}
            @if($activeTab === 'hospitalisations')
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Chambre / Lit</th>
                            <th>Date Entrée</th>
                            <th>Date Sortie</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedDossier->hospitalisations as $h)
                        <tr>
                            <td class="fw-bold text-primary">{{ $h->code_hospitalisation }}</td>
                            <td>Ch. {{ $h->chambre_number }} {{ $h->lit_number ? ' / Lit ' . $h->lit_number : '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($h->date_entree)->format('d/m/Y H:i') }}</td>
                            <td>{{ $h->date_sortie_effective ? \Carbon\Carbon::parse($h->date_sortie_effective)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($h->statut === 'en_cours')
                                <span class="badge bg-warning text-dark">En cours</span>
                                @elseif($h->statut === 'libere')
                                <span class="badge bg-success">Libéré</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($h->statut) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucune hospitalisation liée à ce dossier.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            {{-- TAB 4: RENDEZ-VOUS DU PATIENT --}}
            @if($activeTab === 'rendezvous')
            @php
            // Récupération des rendez-vous directement via la relation du Patient
            $rendezVousList = $selectedDossier->patient->rendezVous ?? collect();
            @endphp

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="bi bi-calendar-event me-1"></i> Rendez-vous de {{ $selectedDossier->patient->nom ?? 'Patient' }} {{ $selectedDossier->patient->prenom ?? '' }}
                </h6>
                <span class="badge bg-light text-dark border">
                    Total : {{ $rendezVousList->count() }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Heure</th>
                            <th>Médecin</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th>Remarques</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendezVousList as $rdv)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-clock me-1 text-primary"></i>
                                    @php
                                    $dateRaw = $rdv->date_rdv ?? $rdv->date_heure ?? $rdv->created_at;
                                    @endphp
                                    {{ $dateRaw ? \Carbon\Carbon::parse($dateRaw)->format('d/m/Y à H:i') : '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    Dr. {{ $rdv->medecin->nom ?? $rdv->medecin->name ?? 'Non assigné' }} {{ $rdv->medecin->prenom ?? '' }}
                                </div>
                                @if(!empty($rdv->medecin->specialite))
                                <small class="text-muted d-block">{{ $rdv->medecin->specialite }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="text-wrap">{{ $rdv->motif ?: 'Non précisé' }}</span>
                            </td>
                            <td>
                                @php
                                $statut = strtolower($rdv->statut ?? 'programme');
                                @endphp
                                @if(in_array($statut, ['programme', 'programmé', 'planifie', 'confirme']))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="bi bi-calendar-check me-1"></i>Programmé
                                </span>
                                @elseif(in_array($statut, ['honore', 'honoré', 'effectue', 'termine', 'effectué']))
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle me-1"></i>Honoré
                                </span>
                                @elseif(in_array($statut, ['annule', 'annulé']))
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle me-1"></i>Annulé
                                </span>
                                @elseif(in_array($statut, ['reporte', 'reporté']))
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                    <i class="bi bi-arrow-repeat me-1"></i>Reporté
                                </span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($rdv->statut ?? 'N/A') }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $rdv->note ?? $rdv->observation ?? $rdv->remarque ?? '-' }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x display-6 d-block mb-2 text-secondary"></i>
                                Aucun rendez-vous enregistré pour ce patient.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
    @endif
</div>