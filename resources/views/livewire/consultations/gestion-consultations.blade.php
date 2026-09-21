<div class="container-fluid py-4">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Entête avec Filtre Rapide Caisse -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 font-weight-bold text-primary">
                    <i class="bx bx-calendar-event me-2"></i>Rendez-vous & Consultations
                </h4>
                <p class="text-muted small mb-0">Planning et fiches de consultation - Centre Médical La Gloire</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Filtre Rapide Caisse : Consultations Impayées -->
                <button wire:click="filtrerEnAttentePaiement" class="btn btn-outline-danger position-relative me-2 radius-30">
                    <i class="bx bx-receipt me-1"></i> Impayés à la Caisse
                    @if(isset($countEnAttentePaiement) && $countEnAttentePaiement > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $countEnAttentePaiement }}
                        </span>
                    @endif
                </button>

                <button wire:click="openModal" class="btn btn-primary px-4 radius-30">
                    <i class="bx bx-plus me-1"></i> Nouveau Rendez-vous
                </button>
            </div>
        </div>
    </div>

    <!-- Barre de Recherche & Filtres -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Patient, code consultation...">
                    </div>
                </div>

                <div class="col-md-3">
                    <input type="date" wire:model.live="filtreDate" class="form-control">
                </div>

                <div class="col-md-3">
                    <select wire:model.live="filtreStatut" class="form-select">
                        <option value="">Tous les statuts médicaux</option>
                        <option value="programme">Programmé</option>
                        <option value="en_attente">En salle d'attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="annule">Annulé</option>
                    </select>
                </div>

                <!-- Filtre Caisse / Paiement -->
                <div class="col-md-3">
                    <select wire:model.live="filtrePaiement" class="form-select">
                        <option value="">Tous les paiements</option>
                        <option value="0">⚠️ En attente de paiement</option>
                        <option value="1">✅ Payés à la caisse</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card border-0 shadow-sm radius-15 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Date & Heure</th>
                            <th>Patient</th>
                            <th>Médecin</th>
                            <th>Tarif / Couverture</th>
                            <th>Paiement Caisse</th>
                            <th>Statut Médical</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultations as $c)
                            <tr>
                                <td><span class="badge bg-soft-primary text-primary font-weight-bold">{{ $c->code_consultation }}</span></td>
                                <td>
                                    <div class="font-weight-bold">{{ $c->date_heure_rdv->format('d/m/Y') }}</div>
                                    <small class="text-muted"><i class="bx bx-time me-1"></i>{{ $c->date_heure_rdv->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $c->patient->nom_complet }}</div>
                                    <small class="text-muted">{{ $c->patient->telephone }}</small>
                                </td>
                                <td>{{ $c->medecin?->name ?? 'Non assigné' }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ number_format($c->tarif_brut ?? 5000, 0, ',', ' ') }} FCFA</div>
                                    @if($c->patient->est_assure && $c->patient->assurance)
                                        <small class="text-primary"><i class="bx bx-shield-quarter me-1"></i>{{ $c->patient->assurance->code }} ({{ $c->patient->taux_couverture }}%)</small>
                                    @endif
                                </td>
                                <td>
                                    @if($c->est_paye)
                                        <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Payé</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>En attente</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($c->statut)
                                        @case('programme') <span class="badge bg-info">Programmé</span> @break
                                        @case('en_attente') <span class="badge bg-warning text-dark">En attente</span> @break
                                        @case('en_cours') <span class="badge bg-primary">En cours</span> @break
                                        @case('termine') <span class="badge bg-success">Terminé</span> @break
                                        @case('annule') <span class="badge bg-danger">Annulé</span> @break
                                    @endswitch
                                </td>
                                <td class="text-end px-4">
                                    <!-- Action d'encaissement rapide -->
                                    @if(!$c->est_paye)
                                        <button wire:click="marquerCommePaye({{ $c->id }})" class="btn btn-sm btn-success me-1" title="Encaisser à la caisse">
                                            <i class="bx bx-dollar-circle"></i>
                                        </button>
                                    @endif

                                    <!-- Bouton Impression Reçu Facture -->
                                    <a href="{{ route('consultations.facture.pdf', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="Imprimer le reçu / facture">
                                        <i class="bx bx-receipt"></i>
                                    </a>
                                
                                    <!-- Imprimer Ordonnance -->
                                    @if(!empty($c->ordonnance))
                                        <a href="{{ route('consultations.ordonnance.pdf', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-success me-1" title="Imprimer l'ordonnance">
                                            <i class="bx bx-printer"></i>
                                        </a>
                                    @endif

                                    <button wire:click="showConsultation({{ $c->id }})" class="btn btn-sm btn-outline-info me-1" title="Voir la fiche"><i class="bx bx-show"></i></button>
                                    <button wire:click="editConsultation({{ $c->id }})" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bx bx-edit"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">Aucun rendez-vous trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($consultations->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end pt-3">{{ $consultations->links() }}</div>
        @endif
    </div>

    <!-- MODALE FORMULAIRE SCROLLABLE -->
    @if($isModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content radius-15 border-0" style="max-height: 90vh;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold text-primary">
                            {{ $isEditMode ? 'Modifier Consultation' : 'Nouveau Rendez-vous / Consultation' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="saveConsultation" class="d-flex flex-column" style="overflow: hidden;">
                        <div class="modal-body p-4" style="overflow-y: auto; max-height: calc(90vh - 130px);">
                            <div class="row g-3">
                                
                                <!-- SECTION 1 : RECHERCHE ET SÉLECTION DYNAMIQUE DU PATIENT -->
                                <div class="col-12"><h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-user me-1"></i> Patient à consulter</h6></div>

                                <div class="col-md-12">
                                    <label class="form-label font-weight-bold">Rechercher & Sélectionner le Patient <span class="text-danger">*</span></label>
                                    
                                    <!-- Champ de recherche réactif -->
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-white"><i class="bx bx-search-alt text-primary"></i></span>
                                        <input type="text" 
                                               wire:model.live.debounce.300ms="searchPatient" 
                                               class="form-control @error('patient_id') is-invalid @enderror" 
                                               placeholder="Tapez le nom, prénom, code ou téléphone du patient...">
                                    </div>
                                    @error('patient_id') <div class="invalid-feedback d-block mb-2">{{ $message }}</div> @enderror

                                    <!-- Liste filtrée dynamique -->
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
                                                            <i class="bx bx-phone me-1"></i>{{ $p->telephone }} | Code: {{ $p->code_patient }}
                                                            @if($p->est_assure && $p->assurance)
                                                                | <span class="badge {{ $patient_id == $p->id ? 'bg-white text-primary' : 'bg-success' }}">{{ $p->assurance->code }} ({{ $p->taux_couverture }}%)</span>
                                                            @endif
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
                                                    Aucun patient trouvé pour "{{ $searchPatient }}".
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION 2 : PROGRAMMATION & MÉDECIN -->
                                <div class="col-12 mt-3"><h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-calendar-check me-1"></i> Programmation & Attribuation</h6></div>

                                <div class="col-md-6">
                                    <label class="form-label">Médecin traitant</label>
                                    <select wire:model="medecin_id" class="form-select">
                                        <option value="">-- Attribuer un médecin --</option>
                                        @foreach($medecins as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date & Heure <span class="text-danger">*</span></label>
                                    <input type="datetime-local" wire:model="date_heure_rdv" class="form-control @error('date_heure_rdv') is-invalid @enderror">
                                    @error('date_heure_rdv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Type de consultation</label>
                                    <select wire:model="type" class="form-select">
                                        <option value="consultation_generale">Consultation Générale</option>
                                        <option value="specialiste">Spécialiste</option>
                                        <option value="suivi">Suivi / Contrôle</option>
                                        <option value="urgence">Urgence</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Statut Médical</label>
                                    <select wire:model="statut" class="form-select">
                                        <option value="programme">Programmé</option>
                                        <option value="en_attente">En salle d'attente</option>
                                        <option value="en_cours">En cours</option>
                                        <option value="termine">Terminé</option>
                                        <option value="annule">Annulé</option>
                                    </select>
                                </div>

                                <!-- SECTION 3 : CONSTANTES -->
                                <div class="col-12 mt-3"><h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-pulse me-1"></i> Constantes lors du rendez-vous</h6></div>
                                <div class="col-md-3"><label class="form-label">Poids (kg)</label><input type="text" wire:model="poids" class="form-control" placeholder="70"></div>
                                <div class="col-md-3"><label class="form-label">Tension (mmHg)</label><input type="text" wire:model="tension" class="form-control" placeholder="12/8"></div>
                                <div class="col-md-3"><label class="form-label">Température (°C)</label><input type="text" wire:model="temperature" class="form-control" placeholder="37"></div>
                                <div class="col-md-3"><label class="form-label">Pouls (bpm)</label><input type="text" wire:model="pouls" class="form-control" placeholder="75"></div>

                                <!-- SECTION 4 : EXAMEN ET EXAMEN MÉDICAL -->
                                <div class="col-12 mt-3"><h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-stethoscope me-1"></i> Bilan Médical & Diagnostic</h6></div>
                                <div class="col-md-6"><label class="form-label">Motif / Plaintes</label><textarea wire:model="motif" class="form-control" rows="2" placeholder="Motif de consultation..."></textarea></div>
                                <div class="col-md-6"><label class="form-label">Examen Physique</label><textarea wire:model="examen_physique" class="form-control" rows="2" placeholder="Examen physique..."></textarea></div>
                                <div class="col-md-6"><label class="form-label">Diagnostic</label><textarea wire:model="diagnostic" class="form-control" rows="2" placeholder="Avis médical / Diagnostic..."></textarea></div>
                                <div class="col-md-6"><label class="form-label">Ordonnance / Prescription</label><textarea wire:model="ordonnance" class="form-control" rows="2" placeholder="Traitements prescrits..."></textarea></div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" wire:click="closeModal">Annuler</button>
                            <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i>Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODALE FICHE DÉTAILLÉE DE LA CONSULTATION -->
    @if($isViewModalOpen && $selectedConsultation)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content radius-15 border-0" style="max-height: 90vh;">
                    
                    <!-- En-tête de la Modale -->
                    <div class="modal-header bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md bg-white text-primary rounded-circle me-3 d-flex align-items-center justify-content-center font-weight-bold" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                <i class="bx bx-stethoscope"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold mb-0 text-white">
                                    Consultation N° {{ $selectedConsultation->code_consultation }}
                                </h5>
                                <small class="opacity-75">
                                    Date : {{ $selectedConsultation->date_heure_rdv->format('d/m/Y à H:i') }} |
                                    Statut : <span class="badge bg-white text-primary">{{ ucfirst($selectedConsultation->statut) }}</span>
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                    </div>

                    <!-- Corps du Dossier de Consultation -->
                    <div class="modal-body p-4" style="overflow-y: auto; background-color: #f8f9fa;">
                        <div class="row g-3">
                            
                            <!-- CARTE 1 : INFORMATIONS PATIENT & MÉDECIN -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm radius-12 h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                            <i class="bx bx-user me-1"></i> Identité du Patient
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-6"><strong>Nom complet :</strong></div>
                                            <div class="col-6 text-end font-weight-bold">{{ $selectedConsultation->patient->nom_complet }}</div>

                                            <div class="col-6"><strong>Code Dossier :</strong></div>
                                            <div class="col-6 text-end"><span class="badge bg-soft-primary text-primary">{{ $selectedConsultation->patient->code_patient }}</span></div>

                                            <div class="col-6"><strong>Téléphone :</strong></div>
                                            <div class="col-6 text-end">{{ $selectedConsultation->patient->telephone }}</div>

                                            <div class="col-6"><strong>Genre / Âge :</strong></div>
                                            <div class="col-6 text-end">
                                                {{ $selectedConsultation->patient->genre === 'M' ? 'Masculin' : 'Féminin' }} 
                                                ({{ $selectedConsultation->patient->date_naissance ? $selectedConsultation->patient->date_naissance->age . ' ans' : '-' }})
                                            </div>

                                            <div class="col-12"><hr class="my-2"></div>

                                            <div class="col-6"><strong>Médecin Traitant :</strong></div>
                                            <div class="col-6 text-end font-weight-bold text-primary">{{ $selectedConsultation->medecin?->name ?? 'Non assigné' }}</div>

                                            <div class="col-6"><strong>Type de Consultation :</strong></div>
                                            <div class="col-6 text-end"><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $selectedConsultation->type)) }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARTE 2 : COUVERTURE SANTE & FACTURATION -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm radius-12 h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                            <i class="bx bx-receipt me-1"></i> Facturation & Prise en Charge
                                        </h6>
                                        
                                        @php
                                            $tarifBrut = $selectedConsultation->tarif_brut ?? 5000;
                                            $tauxAssurance = ($selectedConsultation->patient->est_assure && $selectedConsultation->patient->assurance) ? $selectedConsultation->patient->taux_couverture : 0;
                                            $partAssurance = round(($tarifBrut * $tauxAssurance) / 100);
                                            $partPatient = $tarifBrut - $partAssurance;
                                        @endphp

                                        <div class="row g-2 mb-3">
                                            <div class="col-6"><strong>Montant Brut :</strong></div>
                                            <div class="col-6 text-end font-weight-bold">{{ number_format($tarifBrut, 0, ',', ' ') }} FCFA</div>

                                            @if($tauxAssurance > 0)
                                                <div class="col-6 text-success"><strong>Assurance ({{ $tauxAssurance }}%) :</strong></div>
                                                <div class="col-6 text-end text-success font-weight-bold">- {{ number_format($partAssurance, 0, ',', ' ') }} FCFA</div>
                                            @endif

                                            <div class="col-6"><strong>Net à Payer (Patient) :</strong></div>
                                            <div class="col-6 text-end fs-5 font-weight-bold text-primary">{{ number_format($partPatient, 0, ',', ' ') }} FCFA</div>

                                            <div class="col-6"><strong>Statut Caisse :</strong></div>
                                            <div class="col-6 text-end">
                                                @if($selectedConsultation->est_paye)
                                                    <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>PAYÉ</span>
                                                @else
                                                    <span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>EN ATTENTE</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($selectedConsultation->patient->est_assure && $selectedConsultation->patient->assurance)
                                            <div class="alert alert-info border-0 radius-10 p-2 mb-0 small">
                                                <i class="bx bx-shield-quarter me-1"></i> Organisme : <strong>{{ $selectedConsultation->patient->assurance->nom }}</strong>
                                                @if($selectedConsultation->patient->matricule_assurance)
                                                    <br>Matricule Police : {{ $selectedConsultation->patient->matricule_assurance }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- CARTE 3 : CONSTANTES RELEVÉES -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm radius-12">
                                    <div class="card-body">
                                        <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                            <i class="bx bx-pulse me-1"></i> Constantes & Paramètres Vitaux
                                        </h6>
                                        @php
                                            $constantes = $selectedConsultation->constantes ?? [];
                                        @endphp
                                        <div class="row text-center g-3">
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 border radius-10 bg-white">
                                                    <small class="text-muted d-block">Poids</small>
                                                    <span class="fs-5 font-weight-bold text-dark">{{ $constantes['poids'] ?? '-' }} kg</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 border radius-10 bg-white">
                                                    <small class="text-muted d-block">Tension Artérielle</small>
                                                    <span class="fs-5 font-weight-bold text-dark">{{ $constantes['tension'] ?? '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 border radius-10 bg-white">
                                                    <small class="text-muted d-block">Température</small>
                                                    <span class="fs-5 font-weight-bold text-danger">{{ $constantes['temperature'] ?? '-' }} °C</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 border radius-10 bg-white">
                                                    <small class="text-muted d-block">Pouls</small>
                                                    <span class="fs-5 font-weight-bold text-dark">{{ $constantes['pouls'] ?? '-' }} bpm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARTE 4 : EXAMEN & PRESCRIPTION -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm radius-12">
                                    <div class="card-body">
                                        <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                            <i class="bx bx-notepad me-1"></i> Bilan Médical & Prescription
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light radius-10 h-100">
                                                    <strong class="text-dark d-block mb-1"><i class="bx bx-message-square-detail me-1 text-primary"></i>Motif / Plaintes :</strong>
                                                    <p class="mb-0 text-muted small">{{ $selectedConsultation->motif ?? 'Aucun motif renseigné.' }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="p-3 bg-light radius-10 h-100">
                                                    <strong class="text-dark d-block mb-1"><i class="bx bx-search-alt me-1 text-primary"></i>Examen Physique :</strong>
                                                    <p class="mb-0 text-muted small">{{ $selectedConsultation->examen_physique ?? 'Aucun examen consigné.' }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="p-3 bg-light-info radius-10 border border-info border-opacity-25 h-100">
                                                    <strong class="text-info text-dark d-block mb-1"><i class="bx bx-check-shield me-1"></i>Diagnostic Médical :</strong>
                                                    <p class="mb-0 text-dark font-weight-bold small">{{ $selectedConsultation->diagnostic ?? 'Diagnostic en attente.' }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="p-3 bg-light-success radius-10 border border-success border-opacity-25 h-100">
                                                    <strong class="text-success d-block mb-1"><i class="bx bx-capsule me-1"></i>Ordonnance / Prescription :</strong>
                                                    <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $selectedConsultation->ordonnance ?? 'Aucune prescription rédigée.' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Pied de page avec actions d'impression -->
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary px-4" wire:click="closeViewModal">
                            Fermer
                        </button>

                        <div class="d-flex gap-2">
                            <!-- Bouton Impression Reçu Facture -->
                            <a href="{{ route('consultations.facture.pdf', $selectedConsultation->id) }}" target="_blank" class="btn btn-outline-secondary">
                                <i class="bx bx-receipt me-1"></i> Reçu Facture (PDF)
                            </a>

                            @if(!empty($selectedConsultation->ordonnance))
                                <a href="{{ route('consultations.ordonnance.pdf', $selectedConsultation->id) }}" target="_blank" class="btn btn-success px-3">
                                    <i class="bx bx-printer me-1"></i> Imprimer Ordonnance (PDF)
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>