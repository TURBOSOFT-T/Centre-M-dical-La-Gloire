<div class="container-fluid py-4">
    <!-- Flash Message Notification -->
    @if (session()->has('message'))
    <div class="btn btn-primary px-4 radius-30">
        <div class="flex items-center gap-2">
             <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg> 
            <span>{{ session('message') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg> 
        </button>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- MODE INDEX : LISTE DES RENDEZ-VOUS         --}}
    {{-- ========================================== --}}
    @if ($mode === 'index')


    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 font-weight-bold text-primary">
                    <i class="bx bx-calendar-event me-2"></i>Gestion des Rendez-vous
                </h4>
                <p class="text-muted small mb-0">Planification, suivi et facturation des rendez-vous médicaux- Centre Médical La Gloire</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Filtre Rapide Caisse : Consultations Impayées -->


                <button wire:click="openCreate" class="btn btn-primary px-4 radius-30">
                    <i class="bx bx-plus me-1"></i> Nouveau Rendez-vous
                </button>
            </div>
        </div>
    </div>
  <!--   <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"> -->


        <!-- Search & Filters -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>

                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par code, nom ou prénom..." class="w-full pl-10 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">

                        </div>
                    </div>

                    <div class="col-md-3">
                        <select wire:model.live="filterStatut" class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Tous les statuts</option>
                            <option value="planifie">Planifié</option>
                            <option value="confirme">Confirmé</option>
                            <option value="en_attente">En attente</option>
                            <option value="honore">Honoré</option>
                            <option value="annule">Annulé</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select wire:model.live="filterPaiement" class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
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
                                <th class="px-6 py-3 font-semibold">Code</th>
                                <th class="px-6 py-3 font-semibold">Date & Heure</th>
                                <th class="px-6 py-3 font-semibold">Patient</th>
                                <th class="px-6 py-3 font-semibold">Médecin</th>
                                <th class="px-6 py-3 font-semibold">Statut</th>
                                <th class="px-6 py-3 font-semibold">Paiement</th>
                                <th class="px-6 py-3 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($rendezVousList as $rdv)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-indigo-600">{{ $rdv->code_rdv }}</td>
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $rdv->date_heure ? \Carbon\Carbon::parse($rdv->date_heure)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($rdv->patient)
                                    <div class="font-medium text-slate-800">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</div>
                                    <div class="text-xs text-slate-400">{{ $rdv->patient->telephone }}</div>
                                    @else
                                    <span class="text-slate-400 italic">Non spécifié</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $rdv->medecin ? 'Dr. ' . $rdv->medecin->nom : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                    $badgeClasses = match($rdv->statut) {
                                    'confirme' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'honore' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'en_attente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'annule', 'absent' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClasses }}">
                                        {{ ucfirst(str_replace('_', ' ', $rdv->statut)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                    $payeClasses = match($rdv->statut_paiement) {
                                    'paye' => 'bg-emerald-100 text-emerald-800',
                                    'partiel' => 'bg-amber-100 text-amber-800',
                                    'rembourse' => 'bg-purple-100 text-purple-800',
                                    default => 'bg-slate-100 text-slate-600',
                                    };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded {{ $payeClasses }}">
                                        {{ ucfirst(str_replace('_', ' ', $rdv->statut_paiement)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">



                                    <button wire:click="openShow({{ $rdv->id }})" class="btn btn-sm btn-outline-info me-1" title="Voir la fiche"><i class="bx bx-show"></i></button>
                                    <button wire:click="openEdit({{ $rdv->id }})" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bx bx-edit"></i></button>
                    
                                  
                                    
                                    <button wire:click="delete({{ $rdv->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce rendez-vous ?" title="Supprimer" class="btn btn-sm btn-outline-primary me-1">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Aucun rendez-vous trouvé.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
             <div class="p-4 border-t border-slate-100">
            {{ $rendezVousList->links() }}
        </div>
        </div>

        <!-- Pagination -->
       
   <!--  </div> -->
    @endif

    {{-- ========================================== --}}
    {{-- MODE CREATE & EDIT : FORMULAIRE            --}}
    {{-- ========================================== --}}
    @if ($mode === 'create' || $mode === 'edit')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"tabindex="-1" style="background: rgba(0,0,0,0.5);" role="dialog">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">
                {{ $mode === 'create' ? 'Planifier un Nouveau Rendez-vous' : 'Modifier le Rendez-vous' }}
            </h2>
            <button wire:click="backToIndex" class="btn btn-primary px-4 radius-30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Retour</span>
            </button>
        </div>

        <form wire:submit.prevent="save" class="p-6 space-y-6">
            <!-- Section 1 : Intervenants & Horaires -->
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4">Informations Générales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Autocomplete Patient -->
                    <div class="col-md-12">

                        <div class="col-12">
                            <h6 class="text-primary font-weight-bold border-bottom pb-2"><i class="bx bx-user me-1"></i> Patient à consulter</h6>
                        </div>

                        @if ($selectedPatientName)
                        <div class="input-group mb-2">
                            <span class="text-sm font-medium text-indigo-900">{{ $selectedPatientName }}</span>
                            <button type="button" wire:click="clearPatient" class="text-indigo-400 hover:text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @else
                        <input type="text" wire:model.live.debounce.250ms="searchPatient" placeholder="Saisir un nom, prénom ou téléphone..." class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @if ($patientsFound->count() > 0)
                        <ul class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-100">
                            @foreach ($patientsFound as $p)
                            <li>
                                <button type="button" wire:click="selectPatient({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
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
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @endif
                        @error('patient_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Autocomplete Médecin -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Médecin Praticien</label>
                        @if ($selectedMedecinName)
                        <div class="flex items-center justify-between p-2.5 bg-indigo-50/60 border border-indigo-200 rounded-lg">
                            <span class="text-sm font-medium text-indigo-900">{{ $selectedMedecinName }}</span>
                            <button type="button" wire:click="clearMedecin" class="text-indigo-400 hover:text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @else
                        <input type="text" wire:model.live.debounce.250ms="searchMedecin" placeholder="Rechercher un médecin..." class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @if ($medecinsFound->count() > 0)
                        <ul class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-100">
                            @foreach ($medecinsFound as $m)
                            <li>
                                <button type="button" wire:click="selectMedecin({{ $m->id }}, '{{ addslashes($m->nom) }}')" class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 transition-colors">
                                    <div class="font-medium text-slate-800">Dr. {{ $m->nom }} {{ $m->prenom }}</div>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @endif
                        @error('medecin_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date et Heure -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date & Heure <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" wire:model="date_heure" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('date_heure') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Type de RDV -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type de Consultation <span class="text-rose-500">*</span></label>
                        <select wire:model="type" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="consultation_generale">Consultation Générale</option>
                            <option value="consultation_specialisee">Consultation Spécialisée</option>
                            <option value="suivi">Visite de Suivi</option>
                            <option value="urgence">Urgence</option>
                        </select>
                        @error('type') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Statut du Rendez-vous <span class="text-rose-500">*</span></label>
                        <select wire:model="statut" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="planifie">Planifié</option>
                            <option value="confirme">Confirmé</option>
                            <option value="en_attente">En attente</option>
                            <option value="honore">Honoré</option>
                            <option value="annule">Annulé</option>
                            <option value="absent">Absent</option>
                        </select>
                        @error('statut') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Motif -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Motif de consultation</label>
                        <textarea wire:model="motif" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Notes ou raisons de la prise de rendez-vous..."></textarea>
                        @error('motif') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Section 2 : Tarification & Règlement -->
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4">Facturation & Réglement</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tarif Brut <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" wire:model.live="tarif_brut" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('tarif_brut') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prise en charge Assurance</label>
                        <input type="number" step="0.01" wire:model.live="part_assurance" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('part_assurance') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Reste à payer (Patient)</label>
                        <input type="number" step="0.01" wire:model="part_patient" readonly class="w-full px-3 py-2 text-sm border border-slate-200 bg-slate-100 font-semibold text-slate-700 rounded-lg cursor-not-allowed">
                        @error('part_patient') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Montant Encaissé</label>
                        <input type="number" step="0.01" wire:model="montant_paye" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('montant_paye') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Statut du Paiement <span class="text-rose-500">*</span></label>
                        <select wire:model="statut_paiement" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="non_paye">Non payé</option>
                            <option value="partiel">Partiellement payé</option>
                            <option value="paye">Totalement payé</option>
                            <option value="rembourse">Remboursé</option>
                        </select>
                        @error('statut_paiement') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mode de Paiement</label>
                        <select wire:model="mode_paiement" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Aucun --</option>
                            <option value="especes">Espèces</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="carte_bancaire">Carte Bancaire</option>
                            <option value="assurance">Tiers Payeur / Assurance</option>
                            <option value="autre">Autre</option>
                        </select>
                        @error('mode_paiement') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" wire:click="backToIndex" class="btn btn-primary px-4 radius-30">
                    Annuler
                </button>
                <button type="submit" class="btn btn-primary px-4 radius-30">
                    {{ $mode === 'create' ? 'Créer le rendez-vous' : 'Mettre à jour' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- MODE SHOW : CONSULTATION DÉTAILLÉE         --}}
    {{-- ========================================== --}}
    @if ($mode === 'show' && $selectedRdv)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <span class="btn btn-sm btn-outline-primary me-1">Fiche Rendez-vous</span>
                <h2 class="text-xl font-bold text-slate-800">{{ $selectedRdv->code_rdv }}</h2>
            </div>
            <div class="flex gap-2">
                <button wire:click="openEdit({{ $selectedRdv->id }})" class="btn btn-sm btn-outline-primary me-1">
                    Éditer
                </button>
                <button wire:click="backToIndex" class="btn btn-sm btn-outline-primary">
                    Fermer
                </button>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations du Rendez-vous -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Détails</h3>
                <div class="bg-slate-50 p-4 rounded-lg space-y-3 border border-slate-100">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Date & Heure :</span>
                        <span class="text-sm font-semibold text-slate-800">
                            {{ $selectedRdv->date_heure ? \Carbon\Carbon::parse($selectedRdv->date_heure)->format('d/m/Y à H:i') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Type :</span>
                        <span class="text-sm font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $selectedRdv->type)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Statut :</span>
                        <span class="text-sm font-medium text-slate-800">{{ ucfirst($selectedRdv->statut) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Médecin :</span>
                        <span class="text-sm font-medium text-slate-800">{{ $selectedRdv->medecin ? 'Dr. ' . $selectedRdv->medecin->nom : 'Non assigné' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Créé par :</span>
                        <span class="text-sm font-medium text-slate-800">{{ $selectedRdv->agent ? $selectedRdv->agent->nom : '-' }}</span>
                    </div>
                </div>

                @if ($selectedRdv->motif)
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <span class="text-xs font-semibold text-slate-400 uppercase block mb-1">Motif</span>
                    <p class="text-sm text-slate-700">{{ $selectedRdv->motif }}</p>
                </div>
                @endif
            </div>

            <!-- Informations du Patient & Facturation -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Patient & Facturation</h3>

                @if ($selectedRdv->patient)
                <div class="bg-indigo-50/50 p-4 rounded-lg border border-indigo-100">
                    <div class="font-bold text-indigo-950">{{ $selectedRdv->patient->nom }} {{ $selectedRdv->patient->prenom }}</div>
                    <div class="text-sm text-indigo-700 mt-1">Tél : {{ $selectedRdv->patient->telephone ?? 'N/A' }}</div>
                </div>
                @endif

                <div class="bg-slate-50 p-4 rounded-lg space-y-2 border border-slate-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Tarif Brut :</span>
                        <span class="font-medium text-slate-800">{{ number_format($selectedRdv->tarif_brut, 2) }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Part Assurance :</span>
                        <span class="font-medium text-slate-800">{{ number_format($selectedRdv->part_assurance, 2) }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm font-semibold pt-2 border-t border-slate-200">
                        <span class="text-slate-700">Reste Patient :</span>
                        <span class="text-indigo-600">{{ number_format($selectedRdv->part_patient, 2) }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm pt-1">
                        <span class="text-slate-500">Montant Encaissé :</span>
                        <span class="font-medium text-emerald-600">{{ number_format($selectedRdv->montant_paye, 2) }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm pt-1">
                        <span class="text-slate-500">Mode de Paiement :</span>
                        <span class="font-medium text-slate-800">{{ $selectedRdv->mode_paiement ? ucfirst(str_replace('_', ' ', $selectedRdv->mode_paiement)) : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>