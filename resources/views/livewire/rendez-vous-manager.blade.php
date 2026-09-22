<div class="container-fluid py-4">

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between radius-10 mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-check-circle fs-5"></i>
            <span>{{ session('message') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- MODE INDEX : LISTE DES RENDEZ-VOUS         --}}
    {{-- ========================================== --}}
    @if ($mode === 'index')
    <div class="card mb-4 border-0 shadow-sm radius-15">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 font-weight-bold text-primary">
                    <i class="bx bx-calendar-event me-2"></i>Gestion des Rendez-vous
                </h4>
                <p class="text-muted small mb-0">Planification, suivi et facturation des rendez-vous médicaux - Centre Médical La Gloire</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button wire:click="openCreate" class="btn btn-primary px-4 radius-30">
                    <i class="bx bx-plus me-1"></i> Nouveau Rendez-vous
                </button>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4 border-0 shadow-sm radius-15">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par code, nom ou prénom..." class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <select wire:model.live="filterStatut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="planifie">Planifié</option>
                        <option value="confirme">Confirmé</option>
                        <option value="en_attente">En attente</option>
                        <option value="honore">Honoré</option>
                        <option value="annule">Annulé</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <select wire:model.live="filterPaiement" class="form-select">
                        <option value="">Tous les paiements</option>
                        <option value="non_paye">Non payé</option>
                        <option value="partiel">Partiellement payé</option>
                        <option value="paye">Totalement payé</option>
                        <option value="rembourse">Remboursé</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm radius-15 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Date & Heure</th>
                            <th class="px-4 py-3">Patient</th>
                            <th class="px-4 py-3">Médecin</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3">Paiement</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rendezVousList as $rdv)
                        <tr>
                            <td class="px-4 py-3 fw-bold text-primary">{{ $rdv->code_rdv }}</td>
                            <td class="px-4 py-3 fw-medium text-dark">
                                {{ $rdv->date_heure ? \Carbon\Carbon::parse($rdv->date_heure)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($rdv->patient)
                                <div class="fw-bold text-dark">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</div>
                                <div class="small text-muted"><i class="bx bx-phone me-1"></i>{{ $rdv->patient->telephone }}</div>
                                @else
                                <span class="text-muted fst-italic">Non spécifié</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $rdv->medecin ? 'Dr. ' . $rdv->medecin->nom : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                $badgeClasses = match($rdv->statut) {
                                'confirme' => 'bg-light-info text-info border-info',
                                'honore' => 'bg-light-success text-success border-success',
                                'en_attente' => 'bg-light-warning text-warning border-warning',
                                'annule', 'absent' => 'bg-light-danger text-danger border-danger',
                                default => 'bg-light text-secondary border-secondary',
                                };
                                @endphp
                                <span class="badge border px-2 py-1 radius-30 {{ $badgeClasses }}">
                                    {{ ucfirst(str_replace('_', ' ', $rdv->statut)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                $payeClasses = match($rdv->statut_paiement) {
                                'paye' => 'bg-success text-white',
                                'partiel' => 'bg-warning text-dark',
                                'rembourse' => 'bg-purple text-white',
                                default => 'bg-secondary text-white',
                                };
                                @endphp
                                <span class="badge {{ $payeClasses }}">
                                    {{ ucfirst(str_replace('_', ' ', $rdv->statut_paiement)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button wire:click="openShow({{ $rdv->id }})" class="btn btn-sm btn-outline-info me-1" title="Voir la fiche">
                                    <i class="bx bx-show"></i>
                                </button>
                                <button wire:click="openEdit({{ $rdv->id }})" class="btn btn-sm btn-outline-primary me-1" title="Modifier">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button wire:click="delete({{ $rdv->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce rendez-vous ?" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucun rendez-vous trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3 border-top">
            {{ $rendezVousList->links() }}
        </div>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- MODE CREATE & EDIT : FORMULAIRE            --}}
    {{-- ========================================== --}}
    @if ($mode === 'create' || $mode === 'edit')
    <div class="card border-0 shadow-sm radius-15">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                {{ $mode === 'create' ? 'Planifier un Nouveau Rendez-vous' : 'Modifier le Rendez-vous' }}
            </h5>
            <button wire:click="backToIndex" class="btn btn-outline-secondary px-4 radius-30">
                <i class="bx bx-arrow-back me-1"></i> Retour
            </button>
        </div>

        <div class="card-body p-4">
            <form wire:submit.prevent="save">
                <!-- Section 1 : Intervenants & Horaires -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="letter-spacing: 1px;">Informations Générales</h6>
                    <div class="row g-3">

                        <!-- Autocomplete Patient -->
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Patient à consulter <span class="text-danger">*</span></label>

                            @if ($selectedPatientName)
                            <div class="input-group">
                                <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ $selectedPatientName }}" readonly>
                                <button type="button" wire:click="clearPatient" class="btn btn-outline-danger">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                            @else
                            <input type="text" wire:model.live.debounce.250ms="searchPatient" placeholder="Saisir un nom, prénom ou téléphone..." class="form-control">
                            @if ($patientsFound && $patientsFound->count() > 0)
                            <ul class="list-group position-absolute w-100 z-3 shadow mt-1 max-h-48 overflow-auto">
                                @foreach ($patientsFound as $p)
                                <li class="list-group-item list-group-item-action p-0">
                                    <button type="button" wire:click="selectPatient({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')" class="btn btn-link text-start text-decoration-none text-dark w-100 p-2">
                                        <div class="fw-bold">{{ $p->nom_complet }}</div>
                                        <small class="text-muted">
                                            <i class="bx bx-phone me-1"></i>{{ $p->telephone }} | Code: {{ $p->code_patient }}
                                            @if($p->est_assure && $p->assurance)
                                            | <span class="badge bg-success">{{ $p->assurance->code }} ({{ $p->taux_couverture }}%)</span>
                                            @endif
                                        </small>
                                    </button>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            @endif
                            @error('patient_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Autocomplete Médecin -->
                        <!-- Autocomplete Médecin -->
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">
                                Médecin Praticien <span class="text-danger">*</span>
                            </label>

                            @if ($selectedMedecinName)
                            <!-- État : Médecin sélectionné -->
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary">
                                    <i class="bx bx-user-voice"></i>
                                </span>
                                <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ $selectedMedecinName }}" readonly>
                                <button type="button" wire:click="clearMedecin" class="btn btn-outline-danger" title="Changer de médecin">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                            @else
                            <!-- État : Recherche du médecin -->
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text"
                                    wire:model.live.debounce.250ms="searchMedecin"
                                    placeholder="Tapez le nom, prénom ou spécialité..."
                                    class="form-control @error('medecin_id') is-invalid @enderror">
                            </div>

                            <!-- Liste des résultats trouvés -->
                            @if ($medecinsFound && count($medecinsFound) > 0)
                            <ul class="list-group position-absolute w-100 z-3 shadow mt-1 overflow-auto" style="max-height: 200px;">
                                @foreach ($medecinsFound as $m)
                                <li class="list-group-item list-group-item-action p-0">
                                    <button type="button"
                                        wire:click="selectMedecin({{ $m->id }}, 'Dr. {{ addslashes($m->nom) }} {{ addslashes($m->prenom) }}')"
                                        class="btn btn-link text-start text-decoration-none text-dark w-100 p-2 d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-bold text-primary">Dr. {{ $m->nom }} {{ $m->prenom }}</div>
                                            <small class="text-muted"><i class="bx bx-briefcase me-1"></i>{{ $m->specialite ?? 'Généraliste' }}</small>
                                        </div>
                                        <span class="badge bg-light-primary text-primary">Sélectionner</span>
                                    </button>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            @endif

                            <!-- Message d'erreur si la validation échoue -->
                            @error('medecin_id')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Date et Heure -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date & Heure <span class="text-danger">*</span></label>
                            <input type="datetime-local" wire:model="date_heure" class="form-control">
                            @error('date_heure') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type de RDV -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type de Consultation <span class="text-danger">*</span></label>
                            <select wire:model="type" class="form-select">
                                <option value="consultation_generale">Consultation Générale</option>
                                <option value="consultation_specialisee">Consultation Spécialisée</option>
                                <option value="suivi">Visite de Suivi</option>
                                <option value="urgence">Urgence</option>
                            </select>
                            @error('type') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Statut -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Statut du Rendez-vous <span class="text-danger">*</span></label>
                            <select wire:model="statut" class="form-select">
                                <option value="planifie">Planifié</option>
                                <option value="confirme">Confirmé</option>
                                <option value="en_attente">En attente</option>
                                <option value="honore">Honoré</option>
                                <option value="annule">Annulé</option>
                                <option value="absent">Absent</option>
                            </select>
                            @error('statut') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Motif -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Motif de consultation</label>
                            <textarea wire:model="motif" rows="2" class="form-control" placeholder="Notes ou raisons de la prise de rendez-vous..."></textarea>
                            @error('motif') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                <hr class="my-4">

                <!-- Section 2 : Tarification & Règlement -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="letter-spacing: 1px;">Facturation & Règlement</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tarif Brut <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" wire:model.live="tarif_brut" class="form-control">
                            @error('tarif_brut') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prise en charge Assurance</label>
                            <input type="number" step="0.01" wire:model.live="part_assurance" class="form-control">
                            @error('part_assurance') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Reste à payer (Patient)</label>
                            <input type="number" step="0.01" wire:model="part_patient" readonly class="form-control bg-light fw-bold">
                            @error('part_patient') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Montant Encaissé</label>
                            <input type="number" step="0.01" wire:model="montant_paye" class="form-control">
                            @error('montant_paye') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Statut du Paiement <span class="text-danger">*</span></label>
                            <select wire:model="statut_paiement" class="form-select">
                                <option value="non_paye">Non payé</option>
                                <option value="partiel">Partiellement payé</option>
                                <option value="paye">Totalement payé</option>
                                <option value="rembourse">Remboursé</option>
                            </select>
                            @error('statut_paiement') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mode de Paiement</label>
                            <select wire:model="mode_paiement" class="form-select">
                                <option value="">-- Aucun --</option>
                                <option value="especes">Espèces</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="carte_bancaire">Carte Bancaire</option>
                                <option value="assurance">Tiers Payeur / Assurance</option>
                                <option value="autre">Autre</option>
                            </select>
                            @error('mode_paiement') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <button type="button" wire:click="backToIndex" class="btn btn-light px-4 radius-30">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary px-4 radius-30">
                        <i class="bx bx-save me-1"></i> {{ $mode === 'create' ? 'Enregistrer le rendez-vous' : 'Mettre à jour' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- MODE SHOW : CONSULTATION DÉTAILLÉE & FACTURE --}}
    {{-- ========================================== --}}
    @if ($mode === 'show' && $selectedRdv)
    <!-- Boutons d'action hors impression -->
    <div class="card border-0 shadow-sm radius-15 mb-4 d-print-none">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-primary text-primary px-3 py-2 radius-30 font-medium">Fiche Rendez-vous</span>
                <h5 class="mb-0 fw-bold text-dark">{{ $selectedRdv->code_rdv }}</h5>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-success btn-sm px-3 radius-30">
                    <i class="bx bx-printer me-1"></i> Imprimer la Facture
                </button>
                <button wire:click="openEdit({{ $selectedRdv->id }})" class="btn btn-outline-primary btn-sm px-3 radius-30">
                    <i class="bx bx-edit me-1"></i> Modifier
                </button>
                <button wire:click="backToIndex" class="btn btn-secondary btn-sm px-3 radius-30">
                    <i class="bx bx-x me-1"></i> Fermer
                </button>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Informations Générales -->
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="letter-spacing: 1px;">Détails du Rendez-vous</h6>

                    <div class="bg-light p-3 rounded border space-y-2 mb-3">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Date & Heure :</span>
                            <span class="fw-bold text-dark">
                                {{ $selectedRdv->date_heure ? \Carbon\Carbon::parse($selectedRdv->date_heure)->format('d/m/Y à H:i') : '-' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Type de consultation :</span>
                            <span class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $selectedRdv->type)) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Statut :</span>
                            @php
                            $badgeClasses = match($selectedRdv->statut) {
                            'confirme' => 'bg-light-info text-info border-info',
                            'honore' => 'bg-light-success text-success border-success',
                            'en_attente' => 'bg-light-warning text-warning border-warning',
                            'annule', 'absent' => 'bg-light-danger text-danger border-danger',
                            default => 'bg-light text-secondary border-secondary',
                            };
                            @endphp
                            <span class="badge border px-2 py-1 radius-30 {{ $badgeClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $selectedRdv->statut)) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Médecin Praticien :</span>
                            <span class="fw-semibold text-dark">{{ $selectedRdv->medecin ? 'Dr. ' . $selectedRdv->medecin->nom . ' ' . $selectedRdv->medecin->prenom : 'Non assigné' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">Agent créateur :</span>
                            <span class="text-dark">{{ $selectedRdv->agent ? $selectedRdv->agent->nom : '-' }}</span>
                        </div>
                    </div>

                    @if ($selectedRdv->motif)
                    <div class="bg-light p-3 rounded border">
                        <span class="text-uppercase text-muted fw-bold d-block mb-1 small">Motif de consultation</span>
                        <p class="mb-0 text-secondary small">{{ $selectedRdv->motif }}</p>
                    </div>
                    @endif
                </div>

                <!-- Informations Patient & Facturation -->
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="letter-spacing: 1px;">Patient & Facturation</h6>

                    <!-- Bloc Patient -->
                    @if ($selectedRdv->patient)
                    <div class="alert alert-primary border-0 p-3 mb-3 radius-10">
                        <div class="fw-bold fs-6 text-primary">{{ $selectedRdv->patient->nom }} {{ $selectedRdv->patient->prenom }}</div>
                        <div class="small text-muted mt-1">
                            <i class="bx bx-phone me-1"></i> Tél : {{ $selectedRdv->patient->telephone ?? 'N/A' }}
                        </div>
                        @if(isset($selectedRdv->patient->est_assure) && $selectedRdv->patient->est_assure && $selectedRdv->patient->assurance)
                        <div class="small text-muted mt-1">
                            <i class="bx bx-shield-quarter me-1"></i> Assurance : {{ $selectedRdv->patient->assurance->code ?? $selectedRdv->patient->assurance->nom }} ({{ $selectedRdv->patient->taux_couverture }}%)
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Bloc Règlement -->
                    <div class="bg-light p-3 rounded border">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Tarif Brut :</span>
                            <span class="fw-semibold text-dark">{{ number_format($selectedRdv->tarif_brut, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Part Assurance :</span>
                            <span class="fw-semibold text-dark">{{ number_format($selectedRdv->part_assurance, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom fw-bold">
                            <span class="text-dark">Reste à Payer (Patient) :</span>
                            <span class="text-primary">{{ number_format($selectedRdv->part_patient, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Montant Encaissé :</span>
                            <span class="fw-bold text-success">{{ number_format($selectedRdv->montant_paye, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted small">Statut Paiement :</span>
                            @php
                            $payeClasses = match($selectedRdv->statut_paiement) {
                            'paye' => 'bg-success text-white',
                            'partiel' => 'bg-warning text-dark',
                            'rembourse' => 'bg-purple text-white',
                            default => 'bg-secondary text-white',
                            };
                            @endphp
                            <span class="badge {{ $payeClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $selectedRdv->statut_paiement)) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">Mode de Paiement :</span>
                            <span class="fw-semibold text-dark">{{ $selectedRdv->mode_paiement ? ucfirst(str_replace('_', ' ', $selectedRdv->mode_paiement)) : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @php
    $config = DB::table('configs')->select('icon', 'logo', 'telephone', 'email', 'addresse')->first();

    // Détermination de l'image à utiliser pour le logo
    $logoPath = public_path('/icons/logo.jpg');
    if ($config && !empty($config->logo) && file_exists(storage_path('app/public/' . $config->logo))) {
    $logoPath = storage_path('app/public/' . $config->logo);
    } elseif ($config && !empty($config->icon) && file_exists(storage_path('app/public/' . $config->icon))) {
    $logoPath = storage_path('app/public/' . $config->icon);
    }

    $logoBase64 = '';
    if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
    @endphp

    <!-- TEMPLATE DE FACTURE IMPRIMABLE -->
    <div class="card border-0 shadow-sm radius-15 printable-invoice p-4 bg-white">
        <!-- En-tête de la facture -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-1">CENTRE MÉDICAL LA GLOIRE</h3>
                <p class="text-muted small mb-0">{{ $config->addresse ?? 'N/A' }}</p>
                <p class="text-muted small mb-0">Tél: {{ $config->telephone ?? 'N/A' }} </p>

                <p class="text-muted small mb-0">Email : contact@cm-lagloire.cm</p>


            </div>

            <div>
                @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="Logo" class="logo" style="max-width: 100px; height: auto;">
                @else
                <div style="font-size: 10pt; color: #888;">Logo indisponible</div>
                @endif
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-dark mb-1">FACTURE DE CONSULTATION</h4>
                <span class="badge bg-light-primary text-primary fs-6 px-3 py-1 radius-30">
                    N° {{ $selectedRdv->code_rdv }}
                </span>
                <p class="text-muted small mt-2 mb-0">
                    Date d'émission : {{ \Carbon\Carbon::parse($selectedRdv->created_at ?? now())->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <!-- Informations du Patient et du Praticien -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-3 bg-light rounded border">
                    <h6 class="text-uppercase text-muted fw-bold small mb-2">Informations Patient</h6>
                    <div class="fw-bold fs-6 text-dark">
                        {{ $selectedRdv->patient->nom ?? 'N/A' }} {{ $selectedRdv->patient->prenom ?? '' }}
                    </div>
                    <div class="small text-muted mt-1">
                        <i class="bx bx-phone me-1"></i> Tél : {{ $selectedRdv->patient->telephone ?? 'N/A' }}
                    </div>
                    @if(isset($selectedRdv->patient->est_assure) && $selectedRdv->patient->est_assure && $selectedRdv->patient->assurance)
                    <div class="small text-muted mt-1">
                        <i class="bx bx-shield-quarter me-1"></i> Assurance : {{ $selectedRdv->patient->assurance->code ?? $selectedRdv->patient->assurance->nom }} ({{ $selectedRdv->patient->taux_couverture }}%)
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded border">
                    <h6 class="text-uppercase text-muted fw-bold small mb-2">Détails Rendez-vous</h6>
                    <div class="fw-semibold text-dark">
                        Type : {{ ucfirst(str_replace('_', ' ', $selectedRdv->type)) }}
                    </div>
                    <div class="small text-muted mt-1">
                        Médecin : {{ $selectedRdv->medecin ? 'Dr. ' . $selectedRdv->medecin->nom . ' ' . $selectedRdv->medecin->prenom : 'Non assigné' }}
                    </div>
                    <div class="small text-muted mt-1">
                        Date du RDV : {{ $selectedRdv->date_heure ? \Carbon\Carbon::parse($selectedRdv->date_heure)->format('d/m/Y à H:i') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des prestations -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Désignation</th>
                        <th class="text-end">Tarif Brut</th>
                        <th class="text-end">Couverture Assurance</th>
                        <th class="text-end">Net Patient</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ ucfirst(str_replace('_', ' ', $selectedRdv->type)) }}</strong>
                            @if($selectedRdv->motif)
                            <br><small class="text-muted">Motif : {{ $selectedRdv->motif }}</small>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">{{ number_format($selectedRdv->tarif_brut, 0, ',', ' ') }} FCFA</td>
                        <td class="text-end text-success fw-semibold">- {{ number_format($selectedRdv->part_assurance, 0, ',', ' ') }} FCFA</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($selectedRdv->part_patient, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totaux & Règlement -->
        <div class="row justify-content-end mb-4">
            <div class="col-md-5">
                <div class="bg-light p-3 rounded border">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Part Patient à Payer :</span>
                        <strong class="text-dark">{{ number_format($selectedRdv->part_patient, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Montant Encaissé :</span>
                        <strong class="text-success">{{ number_format($selectedRdv->montant_paye, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Reste à Réglé :</span>
                        <strong class="text-danger">
                            {{ number_format(max(0, $selectedRdv->part_patient - $selectedRdv->montant_paye), 0, ',', ' ') }} FCFA
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Mode de Paiement :</span>
                        <span class="fw-bold text-dark">{{ $selectedRdv->mode_paiement ? ucfirst(str_replace('_', ' ', $selectedRdv->mode_paiement)) : 'Non précisé' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloc de Signature -->
        <div class="row pt-4 text-center mt-3">
            <div class="col-6">
                <p class="small text-muted mb-5">Signature / Cachet du Patient</p>
                <p class="mb-0">_______________________</p>
            </div>
            <div class="col-6">
                <p class="small text-muted mb-5">La Caissière / Le Secrétariat</p>
                <p class="mb-0">_______________________</p>
            </div>
        </div>

        <!-- Bouton au bas de la facture pour ré-imprimer si besoin -->
        <div class="d-flex justify-content-end gap-2 mt-4 d-print-none">
            <button onclick="window.print()" class="btn btn-primary px-4 radius-30">
                <i class="bx bx-printer me-1"></i> Imprimer la Facture
            </button>
        </div>
    </div>

    <!-- Styles CSS dédiés à l'impression -->
    <style>
        @media print {

            /* Masquer tout le contenu de la page */
            body * {
                visibility: hidden;
            }

            /* Afficher uniquement la section imprimable de la facture */
            .printable-invoice,
            .printable-invoice * {
                visibility: visible;
            }

            .printable-invoice {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }

            .d-print-none {
                display: none !important;
            }
        }
    </style>
    @endif
</div>