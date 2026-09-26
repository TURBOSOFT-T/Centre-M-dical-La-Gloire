<div class="container-fluid py-4">
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Entête -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 font-weight-bold text-primary">
                    <i class="bx bx-user-plus me-2"></i>Gestion des Patients
                </h4>
                <p class="text-muted small mb-0">Registre des dossiers médicaux - Centre Médical La Gloire</p>
            </div>
            <button wire:click="openModal" class="btn btn-primary px-4 radius-30">
                <i class="bx bx-plus me-1"></i> Nouveau Patient
            </button>
        </div>
    </div>

    <!-- Barre de Recherche & Filtres -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Rechercher par nom, téléphone, code patient...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select wire:model.live="filtreGenre" class="form-select">
                        <option value="">Tous les genres</option>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Patients -->
    <div class="card border-0 shadow-sm radius-15 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Nom & Prénom</th>
                            <th>Genre</th>
                            <th>Téléphone</th>
                            <th>Couverture Santé</th>
                            <th>Groupe Sanguin</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                        <tr>
                            <td>
                                <span class="badge bg-soft-primary text-primary font-weight-bold">{{ $patient->code_patient }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $patient->nom_complet }}</div>
                                <small class="text-muted">{{ $patient->lieu_residence ?? $patient->ville }}</small>
                            </td>
                            <td>
                                @if($patient->genre === 'M')
                                <span class="badge bg-info text-dark"><i class="bx bx-male"></i> M</span>
                                @else
                                <span class="badge bg-danger"><i class="bx bx-female"></i> F</span>
                                @endif
                            </td>
                            <td>
                                <div><i class="bx bx-phone text-muted me-1"></i>{{ $patient->telephone }}</div>
                                @if($patient->telephone_whatsapp)
                                <small class="text-success"><i class="bx bxl-whatsapp me-1"></i>{{ $patient->telephone_whatsapp }}</small>
                                @endif
                            </td>
                            <td>
                                @if($patient->est_assure && $patient->assurance)
                                <span class="badge bg-success text-white">
                                    <i class="bx bx-shield-quarter me-1"></i>{{ $patient->assurance->code }} ({{ $patient->taux_couverture }}%)
                                </span>
                                @if($patient->matricule_assurance)
                                <div><small class="text-muted">Mat: {{ $patient->matricule_assurance }}</small></div>
                                @endif
                                @else
                                <span class="badge bg-light text-secondary">Non Assuré</span>
                                @endif
                            </td>
                            <td>
                                @if($patient->groupe_sanguin)
                                <span class="badge bg-danger text-white">{{ $patient->groupe_sanguin }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <button wire:click="showPatient({{ $patient->id }})" class="btn btn-sm btn-outline-info me-1" title="Voir la fiche détaillée">
                                    <i class="bx bx-show"></i>
                                </button>

                                <button wire:click="editPatient({{ $patient->id }})" class="btn btn-sm btn-outline-primary me-1" title="Modifier le dossier">
                                    <i class="bx bx-edit"></i>
                                </button>



                                <button class="btn btn-sm btn-danger" onclick="toggle_confirmation({{ $patient->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>

                                <button class="btn btn-sm btn-success d-none" type="button" id="confirmBtn{{ $patient->id }}"
                                    wire:click="delete({{ $patient->id }})">
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
                                Aucun patient trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($patients->hasPages())
        <div class="card-footer bg-white d-flex justify-content-end pt-3">
            {{ $patients->links() }}
        </div>
        @endif
    </div>

    <!-- Modale Formulaire Création/Modification -->
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content radius-15 border-0" style="max-height: 90vh;">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold text-primary">
                        <i class="bx bx-user-pin me-2"></i>{{ $isEditMode ? 'Modifier le Dossier Patient' : 'Nouveau Dossier Patient' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <form wire:submit.prevent="savePatient" class="d-flex flex-column" style="overflow: hidden;">
                    <div class="modal-body p-4" style="overflow-y: auto; max-height: calc(90vh - 130px);">
                        <div class="row g-3">
                            <!-- 1. ÉTAT CIVIL -->
                            <div class="col-12">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-user me-1"></i> État Civil</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" wire:model="nom" class="form-control @error('nom') is-invalid @enderror">
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prénom</label>
                                <input type="text" wire:model="prenom" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Genre <span class="text-danger">*</span></label>
                                <select wire:model="genre" class="form-select @error('genre') is-invalid @enderror">
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                                @error('genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Date de Naissance</label>
                                <input type="date" wire:model="date_naissance" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lieu de Naissance</label>
                                <input type="text" wire:model="lieu_naissance" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Profession</label>
                                <input type="text" wire:model="profession" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Religion</label>
                                <input type="text" wire:model="religion" class="form-control">
                            </div>

                            <!-- 2. COUVERTURE D'ASSURANCE SANTE -->
                            <div class="col-12 mt-4">
                                <div class="card bg-light border-0 radius-10 p-3">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="switchAssurance" wire:model.live="est_assure">
                                        <label class="form-check-label font-weight-bold" for="switchAssurance">
                                            Le patient bénéficie-t-il d'une assurance santé / tiers-payant ?
                                        </label>
                                    </div>

                                    @if($est_assure)
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-4">
                                            <label class="form-label">Compagnie d'Assurance <span class="text-danger">*</span></label>
                                            <select wire:model.live="assurance_id" class="form-select @error('assurance_id') is-invalid @enderror">
                                                <option value="">-- Choisir l'organisme --</option>
                                                @foreach($assurancesList as $ass)
                                                <option value="{{ $ass->id }}">{{ $ass->code }} - {{ $ass->nom }}</option>
                                                @endforeach
                                            </select>
                                            @error('assurance_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Taux de Prise en Charge (%)</label>
                                            <input type="number" min="0" max="100" wire:model="taux_couverture" class="form-control @error('taux_couverture') is-invalid @enderror">
                                            @error('taux_couverture') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-5">
                                            <label class="form-label">N° Matricule / Police</label>
                                            <input type="text" wire:model="matricule_assurance" class="form-control" placeholder="N° de carte assuré">
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Nom de l'Assuré Principal</label>
                                            <input type="text" wire:model="nom_assure" class="form-control" placeholder="Laissez vide si le patient est lui-même l'assuré principal">
                                            <small class="text-muted">À remplir si le souscripteur est un parent, conjoint ou employeur.</small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 3. CONSTANTES & PARAMÈTRES VITAUX -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-pulse me-1"></i> Constantes & Paramètres Vitaux</h6>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Poids (kg)</label>
                                <input type="number" step="0.1" wire:model.live="poids" class="form-control" placeholder="75.0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Taille (cm)</label>
                                <input type="number" wire:model.live="taille" class="form-control" placeholder="175">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">IMC (kg/m²)</label>
                                <input type="text" wire:model="imc" class="form-control bg-light font-weight-bold" readonly placeholder="Auto">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Température (°C)</label>
                                <input type="number" step="0.1" wire:model="temperature" class="form-control" placeholder="37.0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tension (mmHg)</label>
                                <input type="text" wire:model="tension" class="form-control" placeholder="12/8">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Pouls (bpm)</label>
                                <input type="number" wire:model="pouls" class="form-control" placeholder="72">
                            </div>

                            <!-- 4. CONTACTS & LOCALISATION -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-phone me-1"></i> Contacts & Localisation</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="number" wire:model="telephone" class="form-control @error('telephone') is-invalid @enderror">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Téléphone WhatsApp</label>
                                <input type="number" wire:model="telephone_whatsapp" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" wire:model="email" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Lieu de Résidence</label>
                                <input type="text" wire:model="lieu_residence" class="form-control" placeholder="Quartier">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Adresse Domicile</label>
                                <input type="text" wire:model="adresse" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ville</label>
                                <input type="text" wire:model="ville" class="form-control">
                            </div>

                            <!-- 5. ANTÉCÉDENTS -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-health me-1"></i> Profil Médical & Antécédents</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Groupe Sanguin</label>
                                <select wire:model="groupe_sanguin" class="form-select">
                                    <option value="">Non renseigné</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Allergies Connues</label>
                                <textarea wire:model="allergies" class="form-control" rows="2" placeholder="Ex: Pénicilline, AINS..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Antécédents Médicaux</label>
                                <textarea wire:model="antecedents_medicaux" class="form-control" rows="2" placeholder="Ex: Diabète, Hypertension..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" wire:click="closeModal">
                            <i class="bx bx-x me-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- MODALE FICHE MÉDICALE DÉTAILLÉE DU PATIENT AVEC ONGLETS -->
    @if($isViewModalOpen && $selectedPatient)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content radius-15 border-0" style="max-height: 90vh;">

                <!-- En-tête de la Fiche -->
                <div class="modal-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-white text-primary rounded-circle me-3 d-flex align-items-center justify-content-center font-weight-bold" style="width: 45px; height: 45px; font-size: 1.2rem;">
                            {{ strtoupper(substr($selectedPatient->nom, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0 text-white">
                                {{ $selectedPatient->nom_complet }}
                            </h5>
                            <small class="opacity-75">Code Dossier : {{ $selectedPatient->code_patient }} | Tél : {{ $selectedPatient->telephone }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                </div>

                <!-- Corps avec Navigation par Onglets -->
                <div class="modal-body p-4 bg-light" style="overflow-y: auto;">

                    <!-- Barre d'onglets Bootstrap -->
                    <ul class="nav nav-pills mb-3 bg-white p-2 radius-10 shadow-sm" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active font-weight-bold" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab">
                                <i class="bx bx-user-pin me-1"></i> Profil Médical & Assurance
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link font-weight-bold position-relative" id="pills-visites-tab" data-bs-toggle="pill" data-bs-target="#pills-visites" type="button" role="tab">
                                <i class="bx bx-user-voice me-1"></i> Visites & Famille
                                @php
                                $enCoursCount = $selectedPatient->visites ? $selectedPatient->visites->whereNull('date_heure_sortie')->count() : 0;
                                $totalVisites = $selectedPatient->visites ? $selectedPatient->visites->count() : 0;
                                @endphp
                                @if($enCoursCount > 0)
                                <span class="badge bg-danger ms-1">{{ $enCoursCount }} en cours</span>
                                @else
                                <span class="badge bg-secondary ms-1">{{ $totalVisites }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="pills-tabContent">

                        <!-- ONGLET 1 : PROFIL MÉDICAL & DÉTAILS -->
                        <div class="tab-pane fade show active" id="pills-profile" role="tabpanel">
                            <div class="row g-3">

                                <!-- CARTE 1 : ÉTAT CIVIL & CONTACTS -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm radius-12 h-100">
                                        <div class="card-body">
                                            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                                <i class="bx bx-user-pin me-1"></i> État Civil & Contacts
                                            </h6>
                                            <div class="row g-2">
                                                <div class="col-6"><strong>Genre :</strong>
                                                    @if($selectedPatient->genre === 'M')
                                                    <span class="badge bg-info text-dark">Masculin</span>
                                                    @else
                                                    <span class="badge bg-danger">Féminin</span>
                                                    @endif
                                                </div>
                                                <div class="col-6"><strong>Né(e) le :</strong> {{ $selectedPatient->date_naissance ? $selectedPatient->date_naissance->format('d/m/Y') : '-' }}</div>
                                                <div class="col-6"><strong>Lieu Nais. :</strong> {{ $selectedPatient->lieu_naissance ?? '-' }}</div>
                                                <div class="col-6"><strong>Profession :</strong> {{ $selectedPatient->profession ?? '-' }}</div>
                                                <div class="col-6"><strong>Religion :</strong> {{ $selectedPatient->religion ?? '-' }}</div>
                                                <div class="col-6"><strong>Résidence :</strong> {{ $selectedPatient->lieu_residence ?? $selectedPatient->ville }}</div>
                                                <div class="col-12">
                                                    <hr class="my-2">
                                                </div>
                                                <div class="col-6"><strong>Téléphone :</strong> {{ $selectedPatient->telephone }}</div>
                                                <div class="col-6"><strong>WhatsApp :</strong> {{ $selectedPatient->telephone_whatsapp ?? '-' }}</div>
                                                <div class="col-12"><strong>Email :</strong> {{ $selectedPatient->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CARTE 2 : ASSURANCE & PRISE EN CHARGE -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm radius-12 h-100">
                                        <div class="card-body">
                                            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                                <i class="bx bx-shield-quarter me-1"></i> Couverture Santé & Assurance
                                            </h6>
                                            @if($selectedPatient->est_assure && $selectedPatient->assurance)
                                            <div class="alert alert-success border-0 radius-10 mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="font-weight-bold fs-6">{{ $selectedPatient->assurance->nom }}</span>
                                                    <span class="badge bg-success fs-6">{{ $selectedPatient->taux_couverture }}% Prise en Charge</span>
                                                </div>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6"><strong>N° Matricule / Police :</strong></div>
                                                <div class="col-6 text-end font-weight-bold">{{ $selectedPatient->matricule_assurance ?? '-' }}</div>

                                                <div class="col-6"><strong>Assuré Principal :</strong></div>
                                                <div class="col-6 text-end">{{ $selectedPatient->nom_assure ?? 'Le patient lui-même' }}</div>
                                            </div>
                                            @else
                                            <div class="text-center py-4 text-muted">
                                                <i class="bx bx-info-circle fs-1 mb-2"></i>
                                                <p class="mb-0">Ce patient ne bénéficie d'aucune prise en charge sous assurance (Plein Tarif).</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- CARTE 3 : PARAMÈTRES VITAUX (JSON) -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm radius-12">
                                        <div class="card-body">
                                            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                                <i class="bx bx-pulse me-1"></i> Dernières Constantes & Paramètres Vitaux
                                            </h6>
                                            @php
                                            $params = $selectedPatient->parametres ?? [];
                                            @endphp
                                            <div class="row text-center g-3">
                                                <div class="col-md-2 col-4">
                                                    <div class="p-2 border radius-10 bg-white">
                                                        <small class="text-muted d-block">Poids</small>
                                                        <span class="fs-5 font-weight-bold text-dark">{{ $params['poids'] ?? '-' }} kg</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-4">
                                                    <div class="p-2 border radius-10 bg-white">
                                                        <small class="text-muted d-block">Taille</small>
                                                        <span class="fs-5 font-weight-bold text-dark">{{ $params['taille'] ?? '-' }} cm</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-4">
                                                    <div class="p-2 border radius-10 bg-white">
                                                        <small class="text-muted d-block">IMC</small>
                                                        <span class="fs-5 font-weight-bold text-primary">{{ $params['imc'] ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-4">
                                                    <div class="p-2 border radius-10 bg-white">
                                                        <small class="text-muted d-block">Tension</small>
                                                        <span class="fs-5 font-weight-bold text-dark">{{ $params['tension'] ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-4">
                                                    <div class="p-2 border radius-10 bg-white">
                                                        <small class="text-muted d-block">Température</small>
                                                        <span class="fs-5 font-weight-bold text-danger">{{ $params['temperature'] ?? '-' }} °C</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-4">
                                                    <div class="p-2 border radius-10 bg-white">
                                                        <small class="text-muted d-block">Pouls</small>
                                                        <span class="fs-5 font-weight-bold text-dark">{{ $params['pouls'] ?? '-' }} bpm</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CARTE 4 : ANTÉCÉDENTS ET ALLERGIES -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm radius-12">
                                        <div class="card-body">
                                            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                                <i class="bx bx-health me-1"></i> Profil Médical & Antécédents
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <strong>Groupe Sanguin :</strong>
                                                    @if($selectedPatient->groupe_sanguin)
                                                    <span class="badge bg-danger ms-1">{{ $selectedPatient->groupe_sanguin }}</span>
                                                    @else
                                                    <span class="text-muted">Non renseigné</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Allergies Connues :</strong>
                                                    <p class="text-muted small mb-0">{{ $selectedPatient->allergies ?? 'Aucune allergie signalée.' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Antécédents Médicaux :</strong>
                                                    <p class="text-muted small mb-0">{{ $selectedPatient->antecedents_medicaux ?? 'Aucun antécédent particulier.' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ONGLET 2 : HISTORIQUE DES VISITES & REGISTRE FAMILLE -->
                        <div class="tab-pane fade" id="pills-visites" role="tabpanel">

                            @if (session()->has('message_visite'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bx bx-check-circle me-1"></i> {{ session('message_visite') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif

                            <div class="row g-3">
                                <!-- Formulaire rapide pour enregistrer un visiteur -->
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm radius-12">
                                        <div class="card-body">
                                            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                                <i class="bx bx-user-plus me-1"></i> Enregistrer une Visite
                                            </h6>
                                            <form wire:submit.prevent="ajouterVisiteRapide">
                                                <div class="mb-2">
                                                    <label class="form-label small mb-1">Nom du Visiteur <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="visiteur_nom" class="form-control form-control-sm @error('visiteur_nom') is-invalid @enderror" placeholder="Ex: Paul Tagne">
                                                    @error('visiteur_nom') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label small mb-1">Téléphone <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="visiteur_telephone" class="form-control form-control-sm @error('visiteur_telephone') is-invalid @enderror" placeholder="Ex: 699000000">
                                                    @error('visiteur_telephone') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label small mb-1">Lien de Parenté</label>
                                                    <input type="text" wire:model="visiteur_lien" class="form-control form-control-sm" placeholder="Ex: Frère, Épouse...">
                                                </div>

                                                <div class="row g-2 mb-2">
                                                    <div class="col-6">
                                                        <label class="form-label small mb-1">Chambre/Lit</label>
                                                        <input type="text" wire:model="chambre_lit" class="form-control form-control-sm" placeholder="Lit B">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small mb-1">Badge N°</label>
                                                        <input type="number" wire:model="badge_numero" class="form-control form-control-sm" placeholder="05">
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">
                                                    <i class="bx bx-check-circle me-1"></i> Enregistrer l'Entrée
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Liste des visites enregistrées pour ce patient -->
                                <div class="col-md-8">
                                    <div class="card border-0 shadow-sm radius-12 overflow-hidden">
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Visiteur (Famille)</th>
                                                            <th>Lien</th>
                                                            <th>Entrée</th>
                                                            <th>Sortie</th>
                                                            <th class="text-end px-3">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($selectedPatient->visites ?? [] as $visite)
                                                        <tr>
                                                            <td>
                                                                <div class="font-weight-bold">{{ $visite->visiteur->nom_complet }}</div>
                                                                <small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $visite->visiteur->telephone }}</small>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border">{{ $visite->visiteur->lien_parente ?? 'Famille' }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="font-weight-bold">{{ $visite->date_heure_entree->format('d/m/Y') }}</div>
                                                                <small class="text-muted">{{ $visite->date_heure_entree->format('H:i') }}</small>
                                                            </td>
                                                            <td>
                                                                @if($visite->date_heure_sortie)
                                                                <span class="text-success font-weight-bold">{{ $visite->date_heure_sortie->format('H:i') }}</span>
                                                                @else
                                                                <span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>En cours</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end px-3">
                                                                @if(!$visite->date_heure_sortie)
                                                                <button wire:click="marquerSortieVisite({{ $visite->id }})" class="btn btn-xs btn-outline-success" title="Enregistrer la sortie du visiteur">
                                                                    <i class="bx bx-exit"></i> Sortie
                                                                </button>
                                                                @else
                                                                <i class="bx bx-check-double text-success" title="Sortie enregistrée"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4 text-muted">
                                                                <i class="bx bx-user-voice fs-3 d-block mb-1"></i>
                                                                Aucune visite enregistrée pour ce patient.
                                                            </td>
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
                </div>

                <!-- Pied de modale -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" wire:click="closeViewModal">
                        Fermer
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>