<?php

namespace App\Livewire\Consultations;

use App\Http\Traits\TypeConsultations;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class GestionConsultations extends Component
{
    use WithPagination;
    use TypeConsultations;

    protected $paginationTheme = 'bootstrap';

    // Filtres généraux du tableau
    public $search = '';
    public $filtreStatut = '';
    public $filtreDate = '';
    public $filtrePaiement = ''; // '' = Tous, '0' = Impayés, '1' = Payés

    // Recherche dynamique spécifique au patient & médecin dans le Modal
    public $searchPatient = '';
    public $searchMedecin = '';

    // Formulaire Consultation / RDV
    public $consultation_id;
    public $selectedConsultationId = null;
    public $patient_id;
    public $medecin_id;
    public $dossier_medical_id;
    public $date_heure_rdv;
    public $type = 'specialiste';
    public $statut = 'programme';

    // Anamnèse & Motif
    public $motif;
    public $historique_maladie;
    public $antecedents_maladie;
    public $mode_de_vie;

    // Examens Cliniques & Diagnostics
    public $examen_physique;
    public $examen_general;
    public $hypothese_diagnostique;
    public $diagnostic;
    public $resultats_analyses;

    // Traitements & Prescriptions
    public $ordonnance;
    public $traitement;
    public $traitement_sortie;
    public $notes_privees;

    // Facturation & Règlement
    public $tarif_brut = 5000;
    public $montantExamens = 0; // Total cumulé des examens prescrits
    public $est_paye = false;

    // Constantes lors du RDV
    public $poids, $tension, $temperature, $pouls, $glycemie;

    // Évaluations Cliniques & Visites Journalières (JSON)
    public $evaluations = [];
    public $visite_medicale_journaliere = [];
    public $nouvelleVisiteNote = '';

    // Champs pour l'ajout dynamique d'une évaluation à 2 champs
    public $nouvelleEvaluationNom = '';
    public $nouvelleEvaluationValeur = '';

    // Modales & Sélection
    public $isModalOpen = false;
    public $isEditMode = false;
    public $selectedConsultation = null;
    public $isViewModalOpen = false;
    public $typeConsultations = [];

    // Propriétés de gestion du paiement partiel / total à la caisse
    public $isPaymentModalOpen = false;
    public $consultationEnPaiement = null;
    public $montantEncaissement = 0;
    public $modePaiement = 'Espèces';

public $terrain;
public $resultats;

public $bilan = [
    'biologie' => [], // Contiendra les IDs ou noms des examens biologiques cochés
    'imagerie' => [], // Contiendra les IDs ou noms des examens d'imagerie cochés
];

public $listeExamensBiologie = [];
public $listeExamensImagerie = [];

    protected function rules()
    {
        return [
            'patient_id'             => 'required|exists:patients,id',
            'medecin_id'             => 'nullable|exists:users,id',
            'date_heure_rdv'         => 'required',
            'type'                   => 'nullable|string',
            // S'assurer que tous les statuts du select figurent dans in:...
            'statut'                 => 'required|in:programme,en_attente,en_cours,termine,annule',
            'motif'                  => 'nullable|string',
            'historique_maladie'     => 'nullable|string',
            'antecedents_maladie'    => 'nullable|string',
            'mode_de_vie'            => 'nullable|string',
            'examen_physique'        => 'nullable|string',
            'examen_general'         => 'nullable|string',
            'hypothese_diagnostique' => 'nullable|string',
            'diagnostic'             => 'nullable|string',
            'resultats_analyses'     => 'nullable|string',
            'ordonnance'             => 'nullable|string',
            'traitement'             => 'nullable|string',
            'traitement_sortie'      => 'nullable|string',
            'notes_privees'          => 'nullable|string',
            'tarif_brut'             => 'required|numeric|min:0',
            'est_paye'               => 'boolean',
        ];
    }

    public function mount()
    {
        $this->date_heure_rdv = date('Y-m-d\TH:i');
        $this->medecin_id = auth()->id();
    }

    /**
     * Validation en temps réel dès qu'un champ est modifié
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFiltreStatut()
    {
        $this->resetPage();
    }
    public function updatingFiltreDate()
    {
        $this->resetPage();
    }
    public function updatingFiltrePaiement()
    {
        $this->resetPage();
    }

    /**
     * Écouteur déclenché lorsqu'un examen est prescrit, modifié ou supprimé
     * Re-évalue automatiquement le statut de paiement si le coût total augmente
     */
    #[On('examenPrescrit')]
    public function rafraichirConsultation()
    {
        if ($this->selectedConsultationId) {
            $consultation = Consultation::with('demandesExamens')->find($this->selectedConsultationId);

            if ($consultation) {
                // Recalcul du total des examens
                $this->montantExamens = $consultation->demandesExamens->sum('tarif_brut');

                // Recalcul du total général et réévaluation du statut
                $totalGeneral = ($consultation->tarif_brut ?? 0) + $this->montantExamens;
                $montantPaye = $consultation->montant_paye ?? 0;

                // Si le total dépasse ce qui est payé, est_paye repasse à false
                $consultation->est_paye = ($montantPaye >= $totalGeneral);
                $consultation->save();

                // Synchronisation de la propriété locale
                $this->est_paye = $consultation->est_paye;
            }
        }

        if ($this->selectedConsultation) {
            $this->selectedConsultation->load(['demandesExamens.examen', 'patient.assurance']);
        }
    }

    /**
     * Permet d'affecter un nouveau patient à la consultation en cours
     */
    public function changerPatient($patientId)
    {
        $this->patient_id = $patientId;

        $patient = Patient::with(['dossierMedical', 'assurance'])->find($patientId);
        if ($patient) {
            $this->dossier_medical_id = $patient->dossierMedical?->id;

            if ($this->selectedConsultation) {
                $this->selectedConsultation->setRelation('patient', $patient);
            }

            if (!empty($patient->parametres) && is_array($patient->parametres)) {
                $params = $patient->parametres;
                $this->poids = $params['poids'] ?? $this->poids;
                $this->tension = $params['tension'] ?? $this->tension;
                $this->glycemie = $params['glycemie'] ?? $this->glycemie;
                $this->temperature = $params['temperature'] ?? $this->temperature;
                $this->pouls = $params['pouls'] ?? $this->pouls;
            }
        }

        $this->searchPatient = '';
    }

    public function selectPatient($id)
    {
        $this->changerPatient($id);
    }

    public function selectMedecin($medecinId)
    {
        $this->medecin_id = $medecinId;
    }

    /**
     * Ouvre la modale d'encaissement partiel/total à la caisse
     */
    public function openPaiementModal($id)
    {
        $this->consultationEnPaiement = Consultation::with('demandesExamens')->findOrFail($id);
        $totalDu = $this->consultationEnPaiement->tarif_brut + $this->consultationEnPaiement->demandesExamens->sum('tarif_brut');
        $this->montantEncaissement = max(0, $totalDu - ($this->consultationEnPaiement->montant_paye ?? 0));
        $this->isPaymentModalOpen = true;
    }

    public function closePaiementModal()
    {
        $this->isPaymentModalOpen = false;
        $this->consultationEnPaiement = null;
        $this->montantEncaissement = 0;
    }

    /**
     * Enregistre le versement (partiel ou total)
     */
    public function enregistrerVersement()
    {
        if (!$this->consultationEnPaiement) return;

        $totalDu = $this->consultationEnPaiement->tarif_brut + $this->consultationEnPaiement->demandesExamens->sum('tarif_brut');
        $resteAEncaisser = max(0, $totalDu - ($this->consultationEnPaiement->montant_paye ?? 0));

        $this->validate([
            'montantEncaissement' => "required|numeric|min:1|max:{$resteAEncaisser}",
        ], [
            'montantEncaissement.max' => 'Le versement ne peut pas dépasser le reste à payer (' . number_format($resteAEncaisser, 0, ',', ' ') . ' FCFA).',
        ]);

        $nouveauMontantPaye = (float) ($this->consultationEnPaiement->montant_paye ?? 0) + (float) $this->montantEncaissement;

        $historique = is_array($this->consultationEnPaiement->historique_paiements) ? $this->consultationEnPaiement->historique_paiements : [];
        $historique[] = [
            'date'    => date('d/m/Y H:i'),
            'montant' => $this->montantEncaissement,
            'mode'    => $this->modePaiement,
            'caissier' => auth()->user()->name ?? 'Caisse',
        ];

        $estPayeComplet = $nouveauMontantPaye >= $totalDu;

        $this->consultationEnPaiement->update([
            'montant_paye'         => $nouveauMontantPaye,
            'est_paye'             => $estPayeComplet,
            'historique_paiements' => $historique,
        ]);

        session()->flash('message', 'Encaissement de ' . number_format($this->montantEncaissement, 0, ',', ' ') . ' FCFA enregistré avec succès.');
        $this->closePaiementModal();
    }

    public function filtrerEnAttentePaiement()
    {
        $this->resetPage();
        $this->filtrePaiement = '0';
        $this->filtreStatut = '';
    }

        public function filtrerEnAttenteNouvellesModifications()
    {
        $this->resetPage();
       
    }

    public function reinitialiserFiltrePaiement()
    {
        $this->resetPage();
        $this->filtrePaiement = '';
    }

    public function marquerCommePaye($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->est_paye = true;
        $consultation->save();

        session()->flash('message', 'Le règlement de la consultation a été enregistré avec succès.');
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->isEditMode = false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->consultation_id = null;
        $this->selectedConsultationId = null;
        $this->selectedConsultation = null;
        $this->patient_id = null;
        $this->dossier_medical_id = null;
        $this->searchPatient = '';
        $this->searchMedecin = '';
        $this->medecin_id = auth()->id();
        $this->date_heure_rdv = date('Y-m-d\TH:i');
        $this->type = 'specialiste';
        $this->statut = 'programme';

        $this->motif = '';
        $this->historique_maladie = '';
        $this->antecedents_maladie = '';
        $this->mode_de_vie = '';

        $this->examen_physique = '';
        $this->examen_general = '';
        $this->hypothese_diagnostique = '';
        $this->diagnostic = '';
        $this->resultats_analyses = '';

        $this->ordonnance = '';
        $this->traitement = '';
        $this->traitement_sortie = '';
        $this->notes_privees = '';

        $this->tarif_brut = 5000;
        $this->montantExamens = 0;
        $this->est_paye = false;

        $this->poids = '';
        $this->tension = '';
        $this->temperature = '';
        $this->pouls = '';
        $this->glycemie = '';

        $this->evaluations = [];
        $this->visite_medicale_journaliere = [];
        $this->nouvelleVisiteNote = '';

        $this->nouvelleEvaluationNom = '';
        $this->nouvelleEvaluationValeur = '';

        $this->resetValidation();
    }

    public function ajouterEvaluation()
    {
        $this->validate([
            'nouvelleEvaluationNom'    => 'required|string|max:100',
            'nouvelleEvaluationValeur' => 'required|string|max:255',
        ]);

        $evaluations = $this->evaluations ?? [];
        $evaluations[trim($this->nouvelleEvaluationNom)] = trim($this->nouvelleEvaluationValeur);

        $this->evaluations = $evaluations;
        $this->reset(['nouvelleEvaluationNom', 'nouvelleEvaluationValeur']);
    }

    public function supprimerEvaluation($key)
    {
        if (isset($this->evaluations[$key])) {
            unset($this->evaluations[$key]);
        }
    }

    public function ajouterNoteVisite()
    {
        if (empty(trim($this->nouvelleVisiteNote))) {
            return;
        }

        $journal = $this->visite_medicale_journaliere ?? [];
        $journal[] = [
            'date_heure'  => date('d/m/Y H:i'),
            'medecin'     => auth()->user()->name ?? 'Praticien',
            'observation' => trim($this->nouvelleVisiteNote),
        ];

        $this->visite_medicale_journaliere = $journal;
        $this->nouvelleVisiteNote = '';
    }

    /**
     * CHARGEMENT DE TOUS LES CHAMPS POUR ÉDITION
     */
    public function editConsultation($id)
    {
        $c = Consultation::with(['patient.assurance', 'demandesExamens'])->findOrFail($id);

        $this->selectedConsultation = $c;
        $this->consultation_id = $c->id;
        $this->selectedConsultationId = $c->id;
        $this->patient_id = $c->patient_id;
        $this->dossier_medical_id = $c->dossier_medical_id;
        $this->medecin_id = $c->medecin_id;

        $this->date_heure_rdv = $c->date_heure_rdv ? $c->date_heure_rdv->format('Y-m-d\TH:i') : date('Y-m-d\TH:i');
        // $this->type = $c->type ?? 'specialiste';
        // Avant l'enregistrement dans saveConsultation() :
        $this->type = str_replace(' ', '_', strtolower(trim($c->type)));
        $this->statut = $c->statut ?? 'programme';

        // Anamnèse & Historique
        $this->motif = $c->motif ?? '';
        $this->historique_maladie = $c->historique_maladie ?? '';
        $this->antecedents_maladie = $c->antecedents_maladie ?? '';
        $this->mode_de_vie = $c->mode_de_vie ?? '';

        // Clinique & Examens
        $this->examen_physique = $c->examen_physique ?? '';
        $this->examen_general = $c->examen_general ?? '';
        $this->hypothese_diagnostique = $c->hypothese_diagnostique ?? '';
        $this->diagnostic = $c->diagnostic ?? '';
        $this->resultats_analyses = $c->resultats_analyses ?? '';

        // Traitements & Prescriptions
        $this->ordonnance = $c->ordonnance ?? '';
        $this->traitement = $c->traitement ?? '';
        $this->traitement_sortie = $c->traitement_sortie ?? '';
        $this->notes_privees = $c->notes_privees ?? '';

        // Facturation
        $this->tarif_brut = $c->tarif_brut ?? 5000;
        $this->montantExamens = $c->demandesExamens ? $c->demandesExamens->sum('tarif_brut') : 0;
        $this->est_paye = (bool) $c->est_paye;

        // Constantes
        $constantes = is_array($c->constantes) ? $c->constantes : [];
        $this->poids = $constantes['poids'] ?? '';
        $this->tension = $constantes['tension'] ?? '';
        $this->temperature = $constantes['temperature'] ?? '';
        $this->pouls = $constantes['pouls'] ?? '';
        $this->glycemie = $constantes['glycemie'] ?? '';

        // Données JSON
        $this->evaluations = is_array($c->evaluations) ? $c->evaluations : [];
        $this->visite_medicale_journaliere = is_array($c->visite_medicale_journaliere) ? $c->visite_medicale_journaliere : [];

        $this->searchPatient = '';
        $this->searchMedecin = '';

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    /**
     * SAUVEGARDE ET MISE À JOUR EXHAUSTIVE DE TOUS LES CHAMPS
     */
    public function saveConsultation()
    {
        $validatedData = $this->validate();

        if (!$this->dossier_medical_id && $this->patient_id) {
            $patient = Patient::find($this->patient_id);
            $this->dossier_medical_id = $patient?->dossier_medical_id ?? $patient?->dossierMedical?->id;
        }

        $dataToSave = [
            'patient_id'                  => $this->patient_id,
            'medecin_id'                  => $this->medecin_id,
            'dossier_medical_id'          => $this->dossier_medical_id,
            'date_heure_rdv'              => $this->date_heure_rdv,
            //  'type'                        => $this->type ?: 'specialiste',
            // Avant l'enregistrement dans saveConsultation() :
            'type' => str_replace(' ', '_', strtolower(trim($this->type))),
            'statut'                      => $this->statut, // <--- S'ASSAURER QUE LE STATUT EST MENTIONNÉ ICI
            'tarif_brut'                  => $this->tarif_brut,

            'motif'                       => $this->motif ?: null,
            'historique_maladie'          => $this->historique_maladie ?: null,
            'antecedents_maladie'         => $this->antecedents_maladie ?: null,
            'mode_de_vie'                 => $this->mode_de_vie ?: null,

            'examen_physique'             => $this->examen_physique ?: null,
            'examen_general'              => $this->examen_general ?: null,
            'hypothese_diagnostique'      => $this->hypothese_diagnostique ?: null,
            'diagnostic'                  => $this->diagnostic ?: null,
            'resultats_analyses'          => $this->resultats_analyses ?: null,

            'ordonnance'                  => $this->ordonnance ?: null,
            'traitement'                  => $this->traitement ?: null,
            'traitement_sortie'           => $this->traitement_sortie ?: null,
            'notes_privees'               => $this->notes_privees ?: null,

            'constantes'                  => [
                'poids'       => $this->poids,
                'tension'     => $this->tension,
                'temperature' => $this->temperature,
                'pouls'       => $this->pouls,
                'glycemie'    => $this->glycemie,
            ],
            'evaluations'                 => !empty($this->evaluations) ? $this->evaluations : null,
            'visite_medicale_journaliere' => !empty($this->visite_medicale_journaliere) ? $this->visite_medicale_journaliere : null,
        ];

        $consultation = Consultation::updateOrCreate(
            ['id' => $this->consultation_id],
            $dataToSave
        );

        $this->selectedConsultationId = $consultation->id;

        session()->flash('message', $this->isEditMode ? 'Consultation mise à jour avec succès.' : 'Consultation enregistrée avec succès.');
        $this->closeModal();
    }

    public function showConsultation($id)
    {
        $this->selectedConsultation = Consultation::with([
            'patient.assurance',
            'medecin',
            'demandesExamens.examen'
        ])->findOrFail($id);

        $this->isViewModalOpen = true;
    }

    public function closeViewModal()
    {
        $this->isViewModalOpen = false;
        $this->selectedConsultation = null;
    }

    public function changerStatut($id, $nouveauStatut)
    {
        $c = Consultation::findOrFail($id);
        $c->statut = $nouveauStatut;
        $c->save();
        session()->flash('message', 'Statut de la consultation mis à jour.');
    }

    public function delete($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        session()->flash('message', 'Consultation supprimée avec succès.');
    }

    public function render()
    {
        $consultations = Consultation::query()
            ->with(['patient.assurance', 'medecin', 'demandesExamens'])
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('code_patient', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%');
                })->orWhere('code_consultation', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtreStatut, function ($query) {
                $query->where('statut', $this->filtreStatut);
            })
            ->when($this->filtreDate, function ($query) {
                $query->whereDate('date_heure_rdv', $this->filtreDate);
            })
            ->when($this->filtrePaiement !== '', function ($query) {
                $query->where('est_paye', $this->filtrePaiement);
            })
            ->orderBy('date_heure_rdv', 'desc')
            ->paginate(10);

        $patients = Patient::query()
            ->when($this->searchPatient, function ($q) {
                $q->where('nom', 'like', '%' . $this->searchPatient . '%')
                    ->orWhere('prenom', 'like', '%' . $this->searchPatient . '%')
                    ->orWhere('telephone', 'like', '%' . $this->searchPatient . '%')
                    ->orWhere('code_patient', 'like', '%' . $this->searchPatient . '%');
            })
            ->orderBy('nom', 'asc')
            ->take(10)
            ->get();

        $medecins = User::query()
            ->when($this->searchMedecin, function ($q) {
                $q->where('name', 'like', '%' . $this->searchMedecin . '%')
                    ->orWhere('email', 'like', '%' . $this->searchMedecin . '%');
            })
            ->take(10)
            ->get();

        $countEnAttentePaiement = Consultation::where('est_paye', false)->count();
        $this->typeConsultations = $this->getTypeConsultations();

        return view('livewire.consultations.gestion-consultations', compact(
            'consultations',
            'patients',
            'medecins',
            'countEnAttentePaiement'
        ));
    }
}
