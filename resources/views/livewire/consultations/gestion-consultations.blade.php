<div class="container-fluid py-4">
    @include('components.alert')
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- EN-TÊTE AVEC FILTRE RAPIDE CAISSE -->
    <div class="card mb-4 border-0 shadow-sm radius-12">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 font-weight-bold text-primary">
                    <i class="bx bx-calendar-event me-2"></i>Consultations
                </h4>
                <p class="text-muted small mb-0">Planning et fiches de consultations - Centre Médical La Gloire</p>
            </div>

            <div class="d-flex align-items-center gap-2">

                @php
                $countModificationsNonVues = \App\Models\Consultation::where('est_modifie', true)->where('vu_par_responsable', false)->count();
                @endphp
                @can('consultation_notifications')
                <button wire:click="filtrerEnAttenteNouvellesModifications" class="btn {{ $filtreModificationsAlerte ? 'btn-danger' : 'btn-outline-danger' }} position-relative me-2 radius-30">
                    <i class="bx bx-history me-1"></i> Modifications Récentes
                    @if($countModificationsNonVues > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $countModificationsNonVues }}
                    </span>
                    @endif
                </button>
                @endcan
                <!-- Filtre Rapide Caisse : Consultations Impayées -->
                @can('consultation_pay')
                <button wire:click="filtrerEnAttentePaiement" class="btn btn-outline-danger position-relative me-2 radius-30">
                    <i class="bx bx-receipt me-1"></i> Impayés à la Caisse
                    @if(isset($countEnAttentePaiement) && $countEnAttentePaiement > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $countEnAttentePaiement }}
                    </span>
                    @endif
                </button>
                @endcan
                @can('consultation_add')
                <button wire:click="openModal" class="btn btn-primary px-4 radius-30">
                    <i class="bx bx-plus me-1"></i> Nouvelle consultation
                </button>
                @endcan
            </div>
        </div>
    </div>



    <!-- BARRE DE RECHERCHE & FILTRES -->
    <div class="card mb-4 border-0 shadow-sm radius-12">
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

    <!-- TABLEAU PRINCIPAL DES CONSULTATIONS -->
    <div class="card border-0 shadow-sm radius-15 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>

                            <th>Tarif / Couverture</th>
                            <th>Paiement Caisse</th>
                            <th>Statut Médical</th>
                            <th>Modifications</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultations ?? [] as $c)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $c->patient->nom_complet }}</div>
                                <small class="text-muted"><i class="bx bx-phone me-1"></i> Code: {{ $c->patient->code_patient }}</small>
                            </td>
                            <td>
                                @php

                                $totalPrestations = ($c->tarif_brut ?? 5000) + ($c->demandesExamens ? $c->demandesExamens->sum('tarif_brut') : 0);
                                @endphp
                                <div class="font-weight-bold">{{ number_format($totalPrestations, 0, ',', ' ') }} FCFA</div>
                                @if($c->demandesExamens && $c->demandesExamens->count() > 0)
                                <small class="text-primary d-block">+ {{ $c->demandesExamens->count() }} examen(s)</small>
                                @endif
                                @if($c->patient && $c->patient->est_assure && $c->patient->assurance)
                                <small class="text-success"><i class="bx bx-shield-quarter me-1"></i>{{ $c->patient->assurance->code }} ({{ $c->patient->taux_couverture }}%)</small>
                                @endif
                            </td>
                            <td>
                                @php
                                $totalD = ($c->tarif_brut ?? 5000) + ($c->demandesExamens ? $c->demandesExamens->sum('tarif_brut') : 0);
                                $resteD = max(0, $totalD - ($c->montant_paye ?? 0));
                                @endphp

                                @if($c->est_paye || $resteD == 0)
                                <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Payé (100%)</span>
                                @elseif(($c->montant_paye ?? 0) > 0)
                                <span class="badge bg-info text-dark"><i class="bx bx-pie-chart-alt-2 me-1"></i>Partiel ({{ number_format($c->montant_paye, 0, ',', ' ') }} FCFA)</span>
                                <small class="text-danger d-block font-weight-bold">Reste: {{ number_format($resteD, 0, ',', ' ') }} F</small>
                                @else
                                <span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>Impayé</span>
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

                                {{-- Badge de modification --}}

                            </td>
                            <td>

                                {{-- Bouton pour voir uniquement les champs modifiés --}}
                                @can('consultation_confirm_modif')
                                @if($c->est_modifie)
                                <button wire:click="voirModifications({{ $c->id }})" class="btn btn-sm btn-outline-warning me-1 position-relative" title="Voir les champs modifiés">
                                    Visualiser
                                    @if(!$c->vu_par_responsable)
                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                    @endif
                                </button>
                                @endif
                                @endcan
                                @if($c->est_modifie)
                                @if(!$c->vu_par_responsable)
                                <span class="badge bg-danger text-white d-block mt-1" title="Modifié - En attente de validation responsable">
                                    <i class="bx bx-edit me-1"></i> Modifié (Non validé)
                                </span>
                                @else
                                <span class="badge bg-success text-white d-block mt-1" title="Modifié et validé par un responsable">
                                    <i class="bx bx-check-double me-1"></i> Validé (Resp.)
                                </span>
                                @endif
                                @endif
                            </td>
                            <td class="text-end px-4">



                                {{-- Bouton pour marquer comme vu par le responsable (Visible si modifié et non encore validé, ou selon vos rôles) --}}
                                @can('consultation_confirm_modif')
                                @if($c->est_modifie && !$c->vu_par_responsable)
                                <button wire:click="marquerVuParResponsable({{ $c->id }})" class="btn btn-sm btn-outline-success me-1" title="Marquer comme vu par le responsable">
                                    <i class="bx bx-check-shield"></i> Marquer vue
                                </button>
                                @endif
                                @endcan
                                @can('consultation_caisse')
                                @if(!$c->est_paye && $resteD > 0)
                                <button wire:click="openPaiementModal({{ $c->id }})" class="btn btn-sm btn-success me-1 radius-30" title="Encaisser ou Versement partiel">
                                    <i class="bx bx-dollar-circle me-1"></i>Encaisser
                                </button>
                                @endif
                                @endcan

                                {{-- Imprimer la Facture Globalisee --}}
                                <a href="{{ route('consultations.facture.pdf', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="Imprimer le reçu / facture">
                                    <i class="bx bx-receipt"></i>
                                </a>

                                {{-- Imprimer le Bulletin de demande d'examens (si examens prescrits) --}}
                                @if($c->demandesExamens && $c->demandesExamens->count() > 0)
                                <a href="{{ route('consultations.examens-labo.pdf', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-warning me-1" title="Imprimer le bulletin d'examens de laboratoire">
                                    <i class="bx bx-test-tube"></i>
                                </a>
                                @endif

                                {{-- Imprimer l'Ordonnance médicale --}}
                                @if(!empty($c->ordonnance))
                                <a href="{{ route('consultations.ordonnance.pdf', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-success me-1" title="Imprimer l'ordonnance">
                                    <i class="bx bx-printer"></i>
                                </a>
                                @endif

                                <button wire:click="showConsultation({{ $c->id }})" class="btn btn-sm btn-outline-info me-1" title="Voir la fiche"><i class="bx bx-show"></i></button>
                                @can('consultation_edit')
                                @if (method_exists($c, 'modifiable') ? $c->modifiable() : true)
                                <button wire:click="editConsultation({{ $c->id }})" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bx bx-edit"></i></button>
                                @endif
                                @endcan
                                @can('consultation_delete')
                                <button class="btn btn-sm btn-danger" onclick="toggle_confirmation({{ $c->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>
                                @endcan

                                <button class="btn btn-sm btn-success d-none" type="button" id="confirmBtn{{ $c->id }}" wire:click="delete({{ $c->id }})">
                                    <i class="bx bx-check-circle"></i> Confirmer
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Aucune consultation ou rendez-vous trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($consultations) && method_exists($consultations, 'hasPages') && $consultations->hasPages())
        <div class="card-footer bg-white d-flex justify-content-end pt-3">{{ $consultations->links() }}</div>
        @endif
    </div>

    <!-- MODALE D'ENCAISSEMENT PARTIEL ET TOTAL -->
    @if($isPaymentModalOpen && $consultationEnPaiement)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content radius-15 border-0 shadow-lg">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="bx bx-receipt me-2"></i>Règlement / Acompte Caisse - Patient
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closePaiementModal"></button>
                </div>

                <form wire:submit.prevent="enregistrerVersement">
                    <div class="modal-body p-4">

                        {{-- Récapitulatif Financier --}}
                        @php
                        $totalFacture = $consultationEnPaiement->tarif_brut + ($consultationEnPaiement->demandesExamens ? $consultationEnPaiement->demandesExamens->sum('tarif_brut') : 0);
                        $dejaPaye = $consultationEnPaiement->montant_paye ?? 0;
                        $resteAEncaisser = max(0, $totalFacture - $dejaPaye);
                        @endphp

                        <div class="row">
                            <div class="col-md-7">
                                <div class="card bg-light border-0 p-3 mb-3 radius-10">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Total Prestations :</span>
                                        <strong class="text-dark">{{ number_format($totalFacture, 0, ',', ' ') }} FCFA</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Déjà Réglé :</span>
                                        <strong class="text-success">{{ number_format($dejaPaye, 0, ',', ' ') }} FCFA</strong>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between fs-6">
                                        <strong class="text-danger">Reste Net à Payer :</strong>
                                        <strong class="text-danger fs-5">{{ number_format($resteAEncaisser, 0, ',', ' ') }} FCFA</strong>
                                    </div>
                                </div>

                                {{-- Formulaire de Versement --}}
                                @if($resteAEncaisser > 0)
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Montant du versement (FCFA) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number"
                                            wire:model="montantEncaissement"
                                            max="{{ $resteAEncaisser }}"
                                            min="1"
                                            class="form-control fs-5 font-weight-bold text-primary @error('montantEncaissement') is-invalid @enderror">
                                        <button type="button"
                                            class="btn btn-outline-secondary"
                                            wire:click="$set('montantEncaissement', {{ $resteAEncaisser }})">
                                            Tout Régler
                                        </button>
                                    </div>
                                    @error('montantEncaissement') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Mode de paiement</label>
                                    <select wire:model="modePaiement" class="form-select">
                                        <option value="Espèces">Espèces</option>
                                        <option value="Mobile Money">Mobile Money (MTN / Orange)</option>
                                        <option value="Carte Bancaire">Carte Bancaire</option>
                                        <option value="Chèque">Chèque</option>
                                        <option value="Prise en charge Assurance">Prise en charge Assurance</option>
                                    </select>
                                </div>
                                @else
                                <div class="alert alert-success text-center py-3">
                                    <i class="bx bx-check-shield fs-3 d-block mb-1"></i>
                                    <strong>Facture totalement soldée !</strong>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-5 border-start">
                                {{-- Bouton global de génération de facture / reçu --}}


                                {{-- Historique des tranches déjà versées avec option d'impression par reçu --}}
                                <small class="fw-bold text-muted d-block mb-1">Historique des versements :</small>
                                @if(!empty($consultationEnPaiement->historique_paiements))
                                <div>

                                    <div class="border rounded p-2 bg-white" style="max-height: 180px; overflow-y: auto;">
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($consultationEnPaiement->historique_paiements as $index => $p)
                                            <li class="d-flex align-items-center justify-content-between border-bottom py-2">
                                                <div>
                                                    <span class="d-block fw-bold text-dark">+{{ number_format($p['montant'] ?? 0, 0, ',', ' ') }} FCFA</span>
                                                    <small class="text-muted">{{ $p['date'] ?? '' }} • {{ $p['mode'] ?? 'Espèces' }}</small>
                                                </div>
                                                {{-- Bouton pour imprimer un reçu spécifique à cette tranche si la route le supporte --}}
                                                <a href="{{ route('consultations.recu.tranche', ['id' => $consultationEnPaiement->id, 'index' => $index]) }}" target="_blank" class="btn btn-sm btn-light text-primary" title="Imprimer le reçu de cette tranche">
                                                    <i class="bx bx-file"></i>
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4 radius-30" wire:click="closePaiementModal">Fermer</button>
                        @if($resteAEncaisser > 0)
                        <button type="submit" class="btn btn-success px-4 radius-30">
                            <i class="bx bx-check-circle me-1"></i> Valider le Versement
                        </button>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>
    @endif
    <!-- MODALE FORMULAIRE DE CRÉATION / ÉDITION DE CONSULTATION -->
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content radius-15 border-0" style="max-height: 90vh;">

                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold text-primary">
                        {{ $isEditMode ? 'Modifier Consultation' : 'Nouvelle Consultation' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <form wire:submit.prevent="saveConsultation" class="d-flex flex-column" style="overflow: hidden;">
                    <div class="modal-body p-4" style="overflow-y: auto; max-height: calc(90vh - 130px);">
                        <div class="row g-3">

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" wire:model.live="isCreatingNewPatient" id="togglePatient">
                                <label class="form-check-label" for="togglePatient">Créer un nouveau patient à la volée</label>
                            </div>




                            <!-- SECTION 1 : SELECTION & MODIFICATION DU PATIENT -->
                            <div class="col-12">
                                <label class="form-label font-weight-bold text-primary">
                                    <i class="bx bx-user me-1"></i> Patient concerné par la consultation <span class="text-danger">*</span>
                                </label>
                                @if(!$isCreatingNewPatient)
                                @php
                                $patientActuel = $patients->firstWhere('id', $patient_id) ?? $selectedConsultation?->patient;
                                @endphp

                                @if($patientActuel && empty($searchPatient))
                                <!-- BLOC AFFICHAGE DU PATIENT SÉLECTIONNÉ AVEC BOUTON DE CHANGEMENT -->
                                <div class="card border-0 bg-primary text-white p-3 radius-10 shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md bg-white text-primary rounded-circle me-3 d-flex align-items-center justify-content-center font-weight-bold shadow-sm" style="width: 48px; height: 48px; font-size: 1.3rem;">
                                                <i class="bx bx-user"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1 font-weight-bold text-white">
                                                    {{ $patientActuel->nom_complet }}
                                                </h5>
                                                <div class="text-white-50 small">
                                                    Code : <strong class="text-white">{{ $patientActuel->code_patient }}</strong> |
                                                    Tél : <strong class="text-white">{{ $patientActuel->telephone }}</strong> |
                                                    Genre : <strong class="text-white">{{ ($patientActuel->genre === 'M') ? 'Masculin' : 'Féminin' }}</strong>
                                                    @if($patientActuel->est_assure && $patientActuel->assurance)
                                                    | <span class="badge bg-success text-white border border-light px-2 ms-1">{{ $patientActuel->assurance->code }} ({{ $patientActuel->taux_couverture }}%)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!--  <button type="button" wire:click="$set('searchPatient', ' ')" class="btn btn-light btn-sm text-primary font-weight-bold radius-30 px-3">
                                            <i class="bx bx-refresh me-1"></i> Changer de patient
                                        </button> -->
                                    </div>
                                </div>
                                @endif

                                <!-- BARRE DE RECHERCHE & LISTE DE SÉLECTION -->
                                @if(!$patientActuel || !empty($searchPatient))
                                <div class="card border p-3 radius-10 bg-light">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-white"><i class="bx bx-search-alt text-primary"></i></span>
                                        <input type="text"
                                            wire:model.live.debounce.300ms="searchPatient"
                                            class="form-control @error('patient_id') is-invalid @elseif($patient_id) is-valid @enderror"
                                            placeholder="Saisissez le nom, téléphone ou code du bon patient...">

                                        @if($patientActuel)
                                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('searchPatient', '')">
                                            Annuler
                                        </button>
                                        @endif
                                    </div>
                                    @error('patient_id') <div class="invalid-feedback d-block mb-2"><i class="bx bx-error-circle me-1"></i> {{ $message }}</div> @enderror

                                    <div class="border radius-10 p-2 bg-white" style="max-height: 200px; overflow-y: auto;">
                                        <div class="list-group list-group-flush">
                                            @forelse($patients ?? [] as $p)
                                            <button type="button"
                                                wire:click="changerPatient({{ $p->id }})"
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
                                                <span class="badge bg-light text-dark border">Attribuer cette consultation</span>
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
                                @endif
@else
<!-- ================= STATUT 2 : FORMULAIRE DE CRÉATION RAPIDE DU PATIENT ================= -->
        <div class="card border p-3 radius-10 bg-light shadow-sm">
            <h6 class="text-primary mb-3"><i class="bx bx-user-plus me-1"></i> Informations du nouveau patient</h6>
            
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label small">Nom <span class="text-danger">*</span></label>
                    <input type="text" wire:model="nouveau_nom" class="form-control form-control-sm" placeholder="Nom de famille">
                    @error('nouveau_nom') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
               
                <div class="col-md-6">
                    <label class="form-label small">Téléphone <span class="text-danger">*</span></label>
                    <input type="text" wire:model="nouveau_telephone" class="form-control form-control-sm" placeholder="Ex: 699000000">
                    @error('nouveau_telephone') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                
                
               
            </div>
        </div>

                                @endif


                            </div>

                            <!-- SECTION 2 : PROGRAMMATION & MÉDECIN -->

                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-calendar-check me-1"></i> Programmation & Attribution</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Médecin traitant</label>

                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="bx bx-user-pin text-primary"></i></span>
                                    <input type="text"
                                        wire:model.live.debounce.300ms="searchMedecin"
                                        class="form-control"
                                        placeholder="Rechercher un médecin par son nom...">
                                </div>

                                <div class="border radius-10 p-2 bg-light" style="max-height: 160px; overflow-y: auto;">
                                    <div class="list-group list-group-flush">
                                        @forelse($medecins ?? [] as $m)
                                        <button type="button"
                                            wire:click="selectMedecin({{ $m->id }})"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center radius-8 mb-1 py-2 {{ $medecin_id == $m->id ? 'active text-white bg-primary' : 'bg-white' }}">
                                            <div>
                                                <div class="font-weight-bold" style="font-size: 0.88rem;">
                                                    {{ $m->nom }}
                                                </div>
                                                <small class="{{ $medecin_id == $m->id ? 'text-white-50' : 'text-muted' }}">
                                                    {{ $m->email ?? 'Médecin' }}
                                                </small>
                                            </div>
                                            @if($medecin_id == $m->id)
                                            <span class="badge bg-white text-primary font-weight-bold"><i class="bx bx-check me-1"></i>Assigné</span>
                                            @else
                                            <span class="badge bg-light text-dark border">Attribuer</span>
                                            @endif
                                        </button>
                                        @empty
                                        <div class="text-center py-2 text-muted small">Aucun médecin trouvé.</div>
                                        @endforelse
                                    </div>
                                </div>
                                @error('medecin_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Date & Heure <span class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                        wire:model.live="date_heure_rdv"
                                        class="form-control @error('date_heure_rdv') is-invalid @elseif(!empty($date_heure_rdv) && !$errors->has('date_heure_rdv')) is-valid @enderror">
                                    @error('date_heure_rdv') <div class="invalid-feedback"><i class="bx bx-error-circle me-1"></i> {{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Type de consultation <span class="text-danger">*</span></label>
                                    <select wire:model.live="type" class="form-select @error('type') is-invalid @elseif(!empty($type) && !$errors->has('type')) is-valid @enderror">

                                        @foreach ($typeConsultations ?? [] as $typeOption)
                                        <option value="{{ $typeOption }}">{{ ucfirst(str_replace('_', ' ', $typeOption)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback"><i class="bx bx-error-circle me-1"></i> {{ $message }}</div> @enderror
                                </div>
                                @can('consultation_edit_champ')
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Statut Médical <span class="text-danger">*</span></label>
                                    <select wire:model.live="statut" class="form-select @error('statut') is-invalid @enderror">
                                        <option value="programme">Programmé</option>
                                        <option value="en_attente">En salle d'attente</option>
                                        <option value="en_cours">En cours</option>
                                        <option value="termine">Terminé</option>
                                        <option value="annule">Annulé</option>
                                    </select>
                                    @error('statut')
                                    <div class="invalid-feedback"><i class="bx bx-error-circle me-1"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                @endcan

                                <div>
                                    <label class="form-label font-weight-bold">Tarif de base (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number"
                                        wire:model.live="tarif_brut"
                                        class="form-control @error('tarif_brut') is-invalid @elseif($tarif_brut !== null && !$errors->has('tarif_brut')) is-valid @enderror"
                                        placeholder="5000">
                                    @error('tarif_brut') <div class="invalid-feedback"><i class="bx bx-error-circle me-1"></i> {{ $message }}</div> @enderror
                                </div>
                            </div>


                            <!-- SECTION 3 : CONSTANTES -->
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-pulse me-1"></i> Constantes lors du rendez-vous</h6>
                            </div>
                            <div class="col-md-2 col-6"><label class="form-label">Poids (kg)</label><input type="text" wire:model="poids" class="form-control" placeholder="70"></div>
                            <div class="col-md-2 col-6"><label class="form-label">Tension (mmHg)</label><input type="text" wire:model="tension" class="form-control" placeholder="12/8"></div>
                            <div class="col-md-3 col-6"><label class="form-label">Température (°C)</label><input type="text" wire:model="temperature" class="form-control" placeholder="37"></div>
                            <div class="col-md-2 col-6"><label class="form-label">Pouls (bpm)</label><input type="text" wire:model="pouls" class="form-control" placeholder="75"></div>
                            <div class="col-md-3 col-12"><label class="form-label">Glycémie (g/L)</label><input type="text" wire:model="glycemie" class="form-control" placeholder="0.95"></div>

                            <!-- SECTION 4 : ANAMNÈSE & HISTORIQUE -->
                            @can('consultation_edit_champ')
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-history me-1"></i> Anamnèse & Historique de la Maladie</h6>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">Motif de consultation</label>
                                <textarea wire:model.live.debounce.500ms="motif" class="form-control" rows="3" placeholder="Motif de consultation..."></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">Historique de la maladie</label>
                                <textarea wire:model.live.debounce.500ms="historique_maladie" class="form-control" rows="3" placeholder="Évolution des symptômes..."></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">Antécédents de la maladie</label>
                                <textarea wire:model.live.debounce.500ms="antecedents_maladie" class="form-control" rows="3" placeholder="Antécédents spécifiques..."></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">Mode de vie</label>
                                <textarea wire:model.live.debounce.500ms="mode_de_vie" class="form-control" rows="3" placeholder="Alimentation, tabac, alcool, sport..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Terrain </label>
                                <textarea wire:model="terrain" class="form-control" rows="2" placeholder="Ex: Patient hypertendu, allergie à la pénicilline..."></textarea>
                            </div>
                            <!-- SECTION 5 : EXAMENS, DIAGNOSTIC & TRAITEMENT -->
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-stethoscope me-1"></i> Examen Clinique</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Examen Général</label>
                                <textarea wire:model.live.debounce.500ms="examen_general" class="form-control" rows="2" placeholder="État général, faciès, état nutritionnel..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Examen Physique</label>
                                <textarea wire:model.live.debounce.500ms="examen_physique" class="form-control" rows="2" placeholder="Examen physique détaillé..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Hypothèse Diagnostique</label>
                                <textarea wire:model.live.debounce.500ms="hypothese_diagnostique" class="form-control" rows="2" placeholder="Hypothèse / Diagnostic différentiel..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Diagnostic positif</label>
                                <textarea wire:model.live.debounce.500ms="diagnostic" class="form-control" rows="2" placeholder="Avis médical / Diagnostic confirmé..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Résultats</label>
                                <textarea wire:model.live.debounce.500ms="resultats_analyses" class="form-control" rows="2" placeholder="Synthèse des examens..."></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Ordonnance / Prescription</label>
                                <textarea wire:model.live.debounce.500ms="ordonnance" class="form-control" rows="2" placeholder="Traitements prescrits..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Traitement Administré sur place</label>
                                <textarea wire:model.live.debounce.500ms="traitement" class="form-control" rows="2" placeholder="Protocoles appliqués sur place..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Traitement de Sortie & Recommandations</label>
                                <textarea wire:model.live.debounce.500ms="traitement_sortie" class="form-control" rows="2" placeholder="Recommandations et soins à domicile..."></textarea>
                            </div>
                            @endcan
                            <!-- SECTION 6 : ÉVALUATIONS CLINIQUES DYNAMIQUES -->
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2">
                                    <i class="bx bx-clipboard me-1"></i> Évaluation du patient
                                </h6>

                                <div class="card bg-light border-0 p-3 mb-3">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-5">
                                            <input type="text"
                                                wire:model="nouvelleEvaluationNom"
                                                class="form-control @error('nouvelleEvaluationNom') is-invalid @enderror"
                                                placeholder="Paramètre / Critère (ex: Score EVA, Glasgow...)">
                                            @error('nouvelleEvaluationNom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-5">
                                            <input type="text"
                                                wire:model="nouvelleEvaluationValeur"
                                                class="form-control @error('nouvelleEvaluationValeur') is-invalid @enderror"
                                                placeholder="Résultat / Constatation (ex: 7/10, Normal...)">
                                            @error('nouvelleEvaluationValeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-2">
                                            <button type="button" wire:click="ajouterEvaluation" class="btn btn-primary w-100">
                                                <i class="bx bx-plus me-1"></i> Ajouter
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($evaluations))
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle bg-white mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Critère / Évaluation</th>
                                                <th>Valeur / Observation</th>
                                                <th class="text-center" style="width: 60px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($evaluations as $nom => $valeur)
                                            <tr>
                                                <td class="fw-bold text-dark">{{ $nom }}</td>
                                                <td class="text-primary font-weight-bold">{{ $valeur }}</td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        wire:click="supprimerEvaluation('{{ addslashes($nom) }}')"
                                                        class="btn btn-sm btn-outline-danger py-0 px-1"
                                                        title="Supprimer">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <small class="text-muted d-block text-center py-2">Aucune évaluation enregistrée.</small>
                                @endif
                            </div>

                            <!-- SECTION 7 : JOURNAL DE VISITE MÉDICALE JOURNALIÈRE -->
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-time-five me-1"></i> Visite Médicale Journalière (Journal de Suivi)</h6>

                                <div class="card bg-light border-0 p-3 mb-3">
                                    <div class="input-group">
                                        <input type="text" wire:model="nouvelleVisiteNote" class="form-control" placeholder="Saisir l'observation ou l'évolution du jour...">
                                        <button type="button" wire:click="ajouterNoteVisite" class="btn btn-primary px-3">
                                            <i class="bx bx-plus me-1"></i> Ajouter la note
                                        </button>
                                    </div>
                                </div>

                                @if(!empty($visite_medicale_journaliere))
                                <div class="position-relative ps-3 my-3" style="border-left: 2px solid #0d6efd;">
                                    @foreach(array_reverse($visite_medicale_journaliere) as $note)
                                    <div class="position-relative mb-3 ps-3">
                                        <span class="position-absolute bg-primary rounded-circle" style="width: 10px; height: 10px; left: -21px; top: 6px;"></span>
                                        <div class="bg-white p-2 rounded border shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold text-primary">{{ $note['medecin'] ?? 'Praticien' }}</small>
                                                <small class="text-muted fs-7">{{ $note['date_heure'] ?? '' }}</small>
                                            </div>
                                            <p class="mb-0 text-dark small">{{ $note['observation'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <small class="text-muted d-block text-center py-2">Aucune note de visite journalière enregistrée.</small>
                                @endif
                            </div>

                            <!-- SECTION 8 : PRESCRIPTION & RÉSULTATS D'EXAMENS -->
                            @can('consultation_add_exam')
                            @if($isEditMode && isset($selectedConsultationId))
                            @php
                            $consultationActive = $consultations->find($selectedConsultationId);
                            @endphp
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-test-tube me-1"></i> Prescription d'Examens & Analyses</h6>
                                @livewire('consultations.prescrire-examen', ['consultation' => $consultationActive], key('prescrire-'.$selectedConsultationId))
                            </div>

                            @if($consultationActive && $consultationActive->demandesExamens && $consultationActive->demandesExamens->count() > 0)
                            <div class="col-12 mt-3">
                                <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-vial me-1"></i> Saisie des Résultats d'Analyses Biologiques</h6>
                                @foreach($consultationActive->demandesExamens as $demande)
                                @livewire('consultations.saisir-resultats-examen', ['demandeExamen' => $demande], key('saisir-res-'.$demande->id))
                                @endforeach
                            </div>
                            @endif
                            @endif
                            @endcan

                        </div>
                    </div>

                    <!-- PIED DE MODALE ET BOUTON D'ENREGISTREMENT -->
                    <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary px-4 radius-30" wire:click="closeModal">
                            <i class="bx bx-x me-1"></i> Annuler
                        </button>

                        <button type="submit" class="btn btn-primary px-4 radius-30" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveConsultation">
                                <i class="bx bx-save me-1"></i> {{ $isEditMode ? 'Mettre à jour la consultation' : 'Enregistrer la consultation' }}
                            </span>
                            <span wire:loading wire:target="saveConsultation">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Enregistrement en cours...
                            </span>
                        </button>
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
                                Date : {{ $selectedConsultation->date_heure_rdv ? $selectedConsultation->date_heure_rdv->format('d/m/Y à H:i') : '-' }} |
                                Statut : <span class="badge bg-white text-primary">{{ ucfirst($selectedConsultation->statut) }}</span>
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                </div>

                <div class="modal-body p-4" style="overflow-y: auto; background-color: #f8f9fa;">
                    <div class="row g-3">
                        <!-- IDENTITÉ PATIENT & MÉDECIN -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm radius-12 h-100">
                                <div class="card-body">
                                    <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                        <i class="bx bx-user me-1"></i> Identité du Patient
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-6"><strong>Nom complet :</strong></div>
                                        <div class="col-6 text-end font-weight-bold">{{ $selectedConsultation->patient->nom_complet ?? '-' }}</div>

                                        <div class="col-6"><strong>Code Dossier :</strong></div>
                                        <div class="col-6 text-end"><span class="badge bg-soft-primary text-primary">{{ $selectedConsultation->patient->code_patient ?? '-' }}</span></div>

                                        <div class="col-6"><strong>Téléphone :</strong></div>
                                        <div class="col-6 text-end">{{ $selectedConsultation->patient->telephone ?? '-' }}</div>

                                        <div class="col-12">
                                            <hr class="my-2">
                                        </div>

                                        <div class="col-6"><strong>Médecin Traitant :</strong></div>
                                        <div class="col-6 text-end font-weight-bold text-primary">{{ $selectedConsultation->medecin?->nom ?? $selectedConsultation->medecin?->nom ?? 'Non assigné' }}</div>

                                        <div class="col-6"><strong>Type de Consultation :</strong></div>
                                        <div class="col-6 text-end"><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $selectedConsultation->type)) }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FACTURATION ET SOLDE -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm radius-12 h-100">
                                <div class="card-body">
                                    <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                                        <i class="bx bx-receipt me-1"></i> Facturation & Caisse
                                    </h6>

                                    @php
                                    $tarifConsultation = $selectedConsultation->tarif_brut ?? 5000;
                                    $tarifExamens = $selectedConsultation->demandesExamens ? $selectedConsultation->demandesExamens->sum('tarif_brut') : 0;
                                    $totalGeneral = $tarifConsultation + $tarifExamens;

                                    $tauxAssurance = ($selectedConsultation->patient?->est_assure && $selectedConsultation->patient?->assurance) ? $selectedConsultation->patient->taux_couverture : 0;
                                    $partAssurance = round(($totalGeneral * $tauxAssurance) / 100);
                                    $partPatient = $totalGeneral - $partAssurance;
                                    $resteAEncaisser = max(0, $partPatient - ($selectedConsultation->montant_paye ?? 0));
                                    @endphp

                                    <div class="row g-2 mb-3">
                                        <div class="col-6"><span>Acte de Consultation :</span></div>
                                        <div class="col-6 text-end font-weight-bold">{{ number_format($tarifConsultation, 0, ',', ' ') }} FCFA</div>

                                        @if($tarifExamens > 0)
                                        <div class="col-6 text-primary"><span>Examens Prescrits :</span></div>
                                        <div class="col-6 text-end font-weight-bold text-primary">+ {{ number_format($tarifExamens, 0, ',', ' ') }} FCFA</div>
                                        @endif

                                        <div class="col-12">
                                            <hr class="my-1">
                                        </div>

                                        <div class="col-6 font-weight-bold fs-6"><span>Total Général :</span></div>
                                        <div class="col-6 text-end font-weight-bold fs-6 text-dark">{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</div>

                                        @if($tauxAssurance > 0)
                                        <div class="col-6 text-success"><small>Couverture Assurance ({{ $tauxAssurance }}%) :</small></div>
                                        <div class="col-6 text-end text-success"><small>- {{ number_format($partAssurance, 0, ',', ' ') }} FCFA</small></div>

                                        <div class="col-6 font-weight-bold text-dark"><span>Reste Patient :</span></div>
                                        <div class="col-6 text-end font-weight-bold text-dark">{{ number_format($partPatient, 0, ',', ' ') }} FCFA</div>
                                        @endif

                                        <div class="col-6 text-success font-weight-bold"><span>Total Encaissé :</span></div>
                                        <div class="col-6 text-end font-weight-bold text-success">{{ number_format($selectedConsultation->montant_paye ?? 0, 0, ',', ' ') }} FCFA</div>

                                        <div class="col-6 font-weight-bold text-danger"><span>Solde Impayé :</span></div>
                                        <div class="col-6 text-end font-weight-bold text-danger">{{ number_format($resteAEncaisser, 0, ',', ' ') }} FCFA</div>

                                        <div class="col-6"><strong>Statut Caisse :</strong></div>
                                        <div class="col-6 text-end">
                                            @if($selectedConsultation->est_paye)
                                            <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>PAYÉ</span>
                                            @else
                                            <span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>EN ATTENTE</span>
                                            @endif
                                        </div>


                                        <tr style="border-top: 1px solid #0d6efd; font-weight: bold; font-size: 12px;">
                                            <td style="padding-top: 4px; color: #d9534f;">Reste à Payer :</td>
                                            <td style="text-align: right; padding-top: 4px; color: #d9534f;">{{ number_format($resteAEncaisser, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- CARTE 3 : CONSTANTES PRISES -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm radius-12">
                                <div class="card-body">
                                    <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3"><i class="bx bx-pulse me-1"></i> Constantes Vitales</h6>
                                    @php $constantes = $selectedConsultation->constantes ?? []; @endphp
                                    <div class="row text-center g-2">
                                        <div class="col-3 p-2 border-end"><small class="text-muted d-block">Poids</small><strong>{{ $constantes['poids'] ?? '-' }} kg</strong></div>
                                        <div class="col-3 p-2 border-end"><small class="text-muted d-block">Tension</small><strong>{{ $constantes['tension'] ?? '-' }} mmHg</strong></div>
                                        <div class="col-3 p-2 border-end"><small class="text-muted d-block">Température</small><strong>{{ $constantes['temperature'] ?? '-' }} °C</strong></div>
                                        <div class="col-3 p-2"><small class="text-muted d-block">Pouls</small><strong>{{ $constantes['pouls'] ?? '-' }} bpm</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- CARTE 4 : CLINIQUE & TABLEAU DES RÉSULTATS DES ANALYSES -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm radius-12">
                                <div class="card-body">
                                    <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3"><i class="bx bx-file me-1"></i> Examen Clinique</h6>
                                    <div class="mb-3">
                                        <strong class="d-block text-dark">Motif de consultation :</strong>
                                        <p class="mb-0 text-muted">{{ $selectedConsultation->motif ?? 'Aucun motif renseigné' }}</p>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-dark">Examen Général :</strong>
                                        <p class="mb-0 text-muted">{{ $selectedConsultation->examen_general ?? 'Aucun examen physique renseigné' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <strong class="d-block text-dark">Examen Physique :</strong>
                                        <p class="mb-0 text-muted">{{ $selectedConsultation->examen_physique ?? 'Aucun examen physique renseigné' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <strong class="d-block text-dark">Diagnostic :</strong>
                                        <p class="mb-0 text-muted">{{ $selectedConsultation->diagnostic ?? 'Aucun diagnostic renseigné' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <strong class="d-block text-dark">Ordonnance :</strong>
                                        <p class="mb-0 text-muted">{{ $selectedConsultation->ordonnance ?? 'Aucune ordonnance renseignée' }}</p>
                                    </div>

                                    <!-- TABLEAU DYNAMIQUE DES RÉSULTATS D'ANALYSES PRESCRITES -->
                                    @if($selectedConsultation->demandesExamens && $selectedConsultation->demandesExamens->count() > 0)
                                    <div class="mt-4 border-top pt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-dark"><i class="bx bx-vial me-1 text-primary"></i> Résultats des Examens Prescrits ({{ $selectedConsultation->demandesExamens->count() }}) :</strong>
                                            <a href="{{ route('consultations.examens-labo.pdf', $selectedConsultation->id) }}" target="_blank" class="btn btn-sm btn-outline-warning radius-30">
                                                <i class="bx bx-printer me-1"></i> Imprimer Bulletin Labo
                                            </a>


                                        </div>



                                        @foreach($selectedConsultation->demandesExamens as $dEx)
                                        <div class="card border mb-3">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                                <span class="font-weight-bold text-primary">
                                                    <i class="bx bx-chevron-right me-1"></i> {{ $dEx->examen->nom ?? 'Examen' }} (Code: {{ $dEx->code_demande }})
                                                </span>
                                                <span class="badge {{ $dEx->statut_badge ?? 'bg-secondary' }}">{{ ucfirst($dEx->statut) }}</span>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle mb-1">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Sous-analyse</th>
                                                                <th class="text-center">Valeur / Résultat</th>
                                                                <th class="text-center">Valeurs de Référence</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(is_array($dEx->analyses_demandees) && count($dEx->analyses_demandees) > 0)
                                                            @foreach($dEx->analyses_demandees as $an)
                                                            <tr>
                                                                <td class="fw-bold">{{ $an['nom'] ?? '-' }}</td>
                                                                <td class="text-center text-primary font-weight-bold">
                                                                    {{ !empty($an['resultat']) ? $an['resultat'] : 'En attente' }}
                                                                </td>
                                                                <td class="text-center text-muted">
                                                                    {{ !empty($an['norme']) ? $an['norme'] : '-' }}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @else
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">Aucune donnée disponible.</td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if(!empty($dEx->conclusion))
                                                <div class="alert alert-info py-1 px-2 mb-0 mt-2 small">
                                                    <strong>Conclusion / Remarques :</strong> {{ $dEx->conclusion }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <a href="{{ route('consultations.resultats-analyses.pdf', $selectedConsultation->id) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-success radius-30">
                                        <i class="bx bx-printer me-1"></i> Imprimer les Résultats d'Analyses
                                    </a>
                                    @endif
                                </div>


                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary px-4 radius-30" wire:click="closeViewModal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- SECTION : APERÇU DES MODIFICATIONS DYNAMIQUES POUR LE RESPONSABLE -->
    @if(isset($selectedConsultation) && $selectedConsultation->est_modifie && !empty($selectedConsultation->modifications_historique))
    <div class="card border-danger bg-light mb-4 radius-12">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white"><i class="bx bx-history me-2"></i>Détails des modifications apportées (En attente de validation)</h6>
            <span class="badge bg-white text-danger">Modifié récemment</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Champ Modifié</th>
                            <th>Ancienne Valeur</th>
                            <th>Nouvelle Valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedConsultation->modifications_historique as $cle => $diff)
                        <tr>
                            <td class="fw-bold text-primary">{{ $diff['libelle'] ?? $cle }}</td>
                            <td><span class="text-danger text-decoration-line-through">{{ $diff['ancien'] }}</span></td>
                            <td><span class="text-success fw-bold">{{ $diff['nouveau'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Bouton direct pour valider en tant que responsable depuis la vue --}}
            @if(!$selectedConsultation->vu_par_responsable)
            <div class="mt-3 text-end">
                <button wire:click="marquerVuParResponsable({{ $selectedConsultation->id }})" class="btn btn-success btn-sm radius-30">
                    <i class="bx bx-check-shield me-1"></i> Marquer comme vérifié et validé
                </button>
            </div>
            @else
            <div class="mt-2 text-success small text-end">
                <i class="bx bx-check-double me-1"></i> Validé par le responsable le {{ \Carbon\Carbon::parse($selectedConsultation->date_vu_responsable)->format('d/m/Y à H:i') }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- MODALE DE PRÉVISUALISATION DES CHAMPS MODIFIÉS -->
    @if($isModificationsModalOpen && $consultationModificationsDetails)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content radius-15 border-0 shadow-lg">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title font-weight-bold">
                        <i class="bx bx-history me-2"></i>Détails des modifications (Patient : {{ $consultationModificationsDetails->patient?->nom_complet }})
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModificationsModal"></button>
                </div>

                <div class="modal-body p-4">
                    @if(!empty($consultationModificationsDetails->modifications_historique))
                    <p class="text-muted small mb-3">Voici la liste exacte des champs qui ont été modifiés par le praticien :</p>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 bg-white">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Champ concerné</th>
                                    <th style="width: 35%;">Ancienne valeur</th>
                                    <th style="width: 35%;">Nouvelle valeur modifiée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultationModificationsDetails->modifications_historique as $cle => $diff)
                                <tr>
                                    <td class="fw-bold text-primary">
                                        <i class="bx bx-chevron-right me-1"></i>{{ $diff['libelle'] ?? $cle }}
                                    </td>
                                    <td>
                                        <span class="text-danger text-decoration-line-through bg-light px-2 py-1 rounded d-block">
                                            {{ $diff['ancien'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold bg-light px-2 py-1 rounded d-block">
                                            {{ $diff['nouveau'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-info-circle fs-3 d-block mb-1"></i>
                        Aucun détail d'historique de modification disponible pour cette fiche.
                    </div>
                    @endif
                </div>

                <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                    <div>
                        @if(!$consultationModificationsDetails->vu_par_responsable)
                        <span class="badge bg-danger">En attente de validation responsable</span>
                        @else
                        <span class="badge bg-success"><i class="bx bx-check me-1"></i>Validé par le responsable</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary px-3 radius-30" wire:click="closeModificationsModal">Fermer</button>

                        @can('consultation_edit') {{-- ou votre permission de responsable --}}
                        @if(!$consultationModificationsDetails->vu_par_responsable)
                        <button type="button" wire:click="marquerVuParResponsable({{ $consultationModificationsDetails->id }}); closeModificationsModal();" class="btn btn-success px-4 radius-30">
                            <i class="bx bx-check-shield me-1"></i> Valider ces modifications
                        </button>
                        @endif
                        @endcan
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>

<!-- SCRIPTS JS : SCROLL AUTOMATIQUE ET CONFIRMATION -->
<script>
    function toggle_confirmation(id) {
        const btn = document.getElementById('confirmBtn' + id);
        if (btn) {
            btn.classList.toggle('d-none');
        }
    }
</script>

@script
<script>
    // Écoute les erreurs de validation émises par Livewire après soumission
    Livewire.hook('commit', ({
        component,
        commit,
        respond,
        succeed,
        fail
    }) => {
        succeed(() => {
            setTimeout(() => {
                const firstInvalidInput = document.querySelector('.modal-body .is-invalid');
                if (firstInvalidInput) {
                    firstInvalidInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstInvalidInput.focus();
                }
            }, 100);
        });
    });
</script>
@endscript