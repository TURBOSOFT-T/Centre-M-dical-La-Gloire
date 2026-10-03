<?php

namespace App\Livewire\Consultations;

use App\Http\Traits\TypeConsultations;
use App\Models\Consultation;
use App\Models\notifications;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

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
    public $traitement_sortie, $evolution_maladie;
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
    public $montantTotal = 0;
    public $bilan = [
        'biologie' => [],
        'imagerie' => [],
    ];

    public $listeExamensBiologie = [];
    public $listeExamensImagerie = [];
    public $filtreModificationsAlerte = false;

    public $isModificationsModalOpen = false;
    public $consultationModificationsDetails = null;

    public $isCreatingNewPatient = false;

    // Champs pour la création rapide d'un patient
    public $nouveau_nom;
    public $nouveau_prenom;
    public $nouveau_telephone;
    public $nouveau_genre;
    public $nouveau_date_naissance;
    public $nouveau_groupe_sanguin;

    public $prise_en_charge = '';

    // Recherche et sélection des produits pour la prescription
    public $searchProduit = '';
    public $produitsSelectionnes = []; // [produit_id => ['selected' => true, 'quantite' => 1, 'voie_administration' => '', 'posologie' => '', 'prix_unitaire' => 0]]

    // Méthode pour ouvrir la modale des modifications
    public function voirModifications($id)
    {
        $consultation = Consultation::findOrFail($id);
        $this->consultationModificationsDetails = $consultation;
        $this->isModificationsModalOpen = true;
    }

    public function closeModificationsModal()
    {
        $this->isModificationsModalOpen = false;
        $this->consultationModificationsDetails = null;
    }

    /**
     * Active ou désactive un produit dans la prescription avec vérification du stock
     */
    public function toggleProduit($produitId)
    {
        if (isset($this->produitsSelectionnes[$produitId])) {
            unset($this->produitsSelectionnes[$produitId]);
        } else {
            $produit = \App\Models\produits::find($produitId);
            if ($produit && $produit->stock > 0) {
                $this->produitsSelectionnes[$produitId] = [
                    'selected' => true,
                    'quantite' => 1,
                    'voie_administration' => '',
                    'posologie' => '',
                    'prix_unitaire' => $produit->prix_vente ?? $produit->prix ?? 0,
                ];
            }
        }

        // Si on est en mode édition, on met à jour la commande en direct
        if (!empty($this->consultation_id) && class_exists(\App\Models\commandes::class)) {
            $this->actualiserCommandeEnDirect();
        }
    }

    /**
     * Écouteur Livewire déclenché dès qu'un champ des produits sélectionnés change (ex: quantité)
     */
    public function updatedProduitsSelectionnes()
    {
        if (!empty($this->consultation_id) && class_exists(\App\Models\commandes::class)) {
            $this->actualiserCommandeEnDirect();
        }
    }

    /**
     * Méthode utilitaire pour actualiser, créer ou supprimer la commande liée en temps réel
     */
    public function actualiserCommandeEnDirect()
    {
        $montantProduits = 0;
        foreach ($this->produitsSelectionnes as $det) {
            if (!empty($det['selected'])) {
                $qte = (int) ($det['quantite'] ?? 1);
                $pu = (float) ($det['prix_unitaire'] ?? 0);
                $montantProduits += ($pu * $qte);
            }
        }

        $commande = \App\Models\commandes::where('consultation_id', $this->consultation_id)->first();

        if ($montantProduits > 0) {
            if ($commande) {
                $commande->update(['montant_total' => $montantProduits]);
            } else {
                $consultation = Consultation::find($this->consultation_id);
                if ($consultation) {
                    $statutCommande = ($consultation->statut === 'termine' && ($consultation->est_paye || $consultation->statut_paiement === 'paye')) ? 'paye' : 'en_attente';
                    \App\Models\commandes::create([
                        'consultation_id' => $consultation->id,
                        'client_id'       => $consultation->patient_id,
                        'reference'       => 'SWB-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                        'user_id'         => auth()->id(),
                        'montant_total'   => $montantProduits,
                        'statut'          => $statutCommande,
                    ]);
                }
            }
        } else {
            if ($commande) {
                $commande->delete();
            }
        }
    }

    protected function rules()
    {
        $rules = [
            'medecin_id'             => 'nullable|exists:users,id',
            'date_heure_rdv'         => 'required',
            'type'                   => 'nullable|string',
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

        if ($this->isCreatingNewPatient) {
            $rules['nouveau_nom'] = 'required|string|max:255';
            $rules['nouveau_prenom'] = 'nullable|string|max:255';
            $rules['nouveau_telephone'] = 'required|string|max:50';
            $rules['nouveau_genre'] = 'nullable|in:M,F';
            $rules['nouveau_date_naissance'] = 'nullable|date';
        } else {
            $rules['patient_id'] = 'required|exists:patients,id';
        }

        return $rules;
    }

    public function mount($consultationId = null)
    {
        $this->date_heure_rdv = date('Y-m-d\TH:i');
        $this->medecin_id = auth()->id();
        if ($consultationId) {
            $consultation = Consultation::findOrFail($consultationId);
            $this->consultation_id = $consultation->id;
            $this->prise_en_charge = $consultation->prise_en_charge;
        }
    }

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

    #[On('examenPrescrit')]
    public function rafraichirConsultation()
    {
        if ($this->selectedConsultationId) {
            $consultation = Consultation::with('demandesExamens')->find($this->selectedConsultationId);

            if ($consultation) {
                $this->montantExamens = $consultation->demandesExamens->sum('tarif_brut');
                $totalGeneral = ($consultation->tarif_brut ?? 0) + $this->montantExamens;
                $montantPaye = $consultation->montant_paye ?? 0;

                $consultation->est_paye = ($montantPaye >= $totalGeneral);
                $consultation->save();

                $this->est_paye = $consultation->est_paye;
            }
        }

        if ($this->selectedConsultation) {
            $this->selectedConsultation->load(['demandesExamens.examen', 'patient.assurance']);
        }
    }

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

    public function enregistrerVersement()
    {
        if (!$this->consultationEnPaiement) return;

        // 1. Calcul du total brut (Consultation + Examens + Produits)
        $tarifConsultation = $this->consultationEnPaiement->tarif_brut ?? 5000;
        $tarifExamens = $this->consultationEnPaiement->demandesExamens ? $this->consultationEnPaiement->demandesExamens->sum('tarif_brut') : 0;
        
        $tarifProduits = 0;
        if ($this->consultationEnPaiement->relationLoaded('produits') ? $this->consultationEnPaiement->produits : $this->consultationEnPaiement->produits()->exists()) {
            foreach ($this->consultationEnPaiement->produits as $prod) {
                $qte = $prod->pivot->quantite ?? 1;
                $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
                $tarifProduits += ($qte * $pu);
            }
        }

        $totalBrut = $tarifConsultation + $tarifExamens + $tarifProduits;
        $totalDu = $totalBrut;

        // 2. Application de l'assurance si le patient est couvert
        if ($this->consultationEnPaiement->patient && $this->consultationEnPaiement->patient->est_assure && $this->consultationEnPaiement->patient->assurance) {
            $taux = (float) $this->consultationEnPaiement->patient->taux_couverture;
            $partAssurance = round(($totalBrut * $taux) / 100);
            $totalDu = $totalBrut - $partAssurance;
        }

        $resteAEncaisser = max(0, $totalDu - ($this->consultationEnPaiement->montant_paye ?? 0));

        // 3. Validation du montant saisi par l'utilisateur
        $this->validate([
            'montantEncaissement' => "required|numeric|min:1|max:{$resteAEncaisser}",
        ], [
            'montantEncaissement.max' => 'Le versement ne peut pas dépasser le reste à payer (' . number_format($resteAEncaisser, 0, ',', ' ') . ' FCFA).',
        ]);

        $nouveauMontantPaye = (float) ($this->consultationEnPaiement->montant_paye ?? 0) + (float) $this->montantEncaissement;

        $historique = is_array($this->consultationEnPaiement->historique_paiements) ? $this->consultationEnPaiement->historique_paiements : [];
        $historique[] = [
            'date'     => date('d/m/Y H:i'),
            'montant' => $this->montantEncaissement,
            'mode'    => $this->modePaiement ?? 'Espèces',
            'caissier' => auth()->user()->nom ?? auth()->user()->name ?? 'Caisse',
        ];

        // Tolérance de 1 FCFA pour éviter les erreurs d'arrondi sur les divisions d'assurance
        $estPayeComplet = $nouveauMontantPaye >= ($totalDu - 1);

        $this->consultationEnPaiement->update([
            'montant_paye'         => $nouveauMontantPaye,
            'est_paye'             => $estPayeComplet,
            'historique_paiements' => $historique,
        ]);

        session()->flash('message', 'Encaissement de ' . number_format($this->montantEncaissement, 0, ',', ' ') . ' FCFA enregistré avec succès.');
        $this->closePaiementModal();
    }

    public function enregistrerVersement2()
    {
        if (!$this->consultationEnPaiement) return;

        $totalDu = $this->consultationEnPaiement->tarif_brut + $this->consultationEnPaiement->demandesExamens->sum('tarif_brut');
        $resteAEncaisser = max(0, $totalDu - ($this->consultationEnPaiement->montant_paye ?? 0));

        $tarifProduits = 0;
        if ($this->consultationEnPaiement->relationLoaded('produits') ? $this->consultationEnPaiement->produits : $this->consultationEnPaiement->produits()->exists()) {
            foreach ($this->consultationEnPaiement->produits as $prod) {
                $qte = $prod->pivot->quantite ?? 1;
                $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
                $tarifProduits += ($qte * $pu);
            }
        }
        $this->validate([
            'montantEncaissement' => "required|numeric|min:1|max:{$resteAEncaisser}",
        ], [
            'montantEncaissement.max' => 'Le versement ne peut pas dépasser le reste à payer (' . number_format($resteAEncaisser, 0, ',', ' ') . ' FCFA).',
        ]);

        $nouveauMontantPaye = (float) ($this->consultationEnPaiement->montant_paye ?? 0) + (float) $this->montantEncaissement;

        $historique = is_array($this->consultationEnPaiement->historique_paiements) ? $this->consultationEnPaiement->historique_paiements : [];
        $historique[] = [
            'date'     => date('d/m/Y H:i'),
            'montant' => $this->montantEncaissement,
            'mode'    => $this->modePaiement,
            'caissier' => auth()->user()->nom ?? 'Caisse',
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
        $this->filtreModificationsAlerte = !$this->filtreModificationsAlerte;
        $this->filtrePaiement = '';
        $this->filtreStatut = '';
    }

    public function marquerVuParResponsable($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->update([
            'vu_par_responsable'  => true,
            'date_vu_responsable' => now(),
            'responsable_id'      => auth()->id(),
        ]);

        session()->flash('message', 'Consultation marquée comme vue par le responsable.');
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
        $this->searchProduit = '';
        $this->produitsSelectionnes = [];

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
            'medecin'     => auth()->user()->nom ?? 'Praticien',
            'observation' => trim($this->nouvelleVisiteNote),
        ];

        $this->visite_medicale_journaliere = $journal;
        $this->nouvelleVisiteNote = '';
    }

    public function editConsultation($id)
    {
        $c = Consultation::with(['patient.assurance', 'demandesExamens', 'produits'])->findOrFail($id);

        $this->selectedConsultation = $c;
        $this->consultation_id = $c->id;
        $this->selectedConsultationId = $c->id;
        $this->patient_id = $c->patient_id;
        $this->dossier_medical_id = $c->dossier_medical_id;
        $this->medecin_id = $c->medecin_id;

        $this->date_heure_rdv = $c->date_heure_rdv ? $c->date_heure_rdv->format('Y-m-d\TH:i') : date('Y-m-d\TH:i');
        $this->type = str_replace(' ', '_', strtolower(trim($c->type)));
        $this->statut = $c->statut ?? 'programme';
        $this->prise_en_charge = str_replace(' ', '_', strtolower(trim($c->prise_en_charge))) ?? '';

        $this->motif = $c->motif ?? '';
        $this->historique_maladie = $c->historique_maladie ?? '';
        $this->antecedents_maladie = $c->antecedents_maladie ?? '';
        $this->mode_de_vie = $c->mode_de_vie ?? '';

        $this->examen_physique = $c->examen_physique ?? '';
        $this->examen_general = $c->examen_general ?? '';
        $this->hypothese_diagnostique = $c->hypothese_diagnostique ?? '';
        $this->diagnostic = $c->diagnostic ?? '';
        $this->resultats_analyses = $c->resultats_analyses ?? '';

        $this->ordonnance = $c->ordonnance ?? '';
        $this->traitement = $c->traitement ?? '';
        $this->traitement_sortie = $c->traitement_sortie ?? '';
        $this->notes_privees = $c->notes_privees ?? '';

        $this->tarif_brut = $c->tarif_brut ?? 5000;
        $this->montantExamens = $c->demandesExamens ? $c->demandesExamens->sum('tarif_brut') : 0;
        $this->est_paye = (bool) $c->est_paye;

        $constantes = is_array($c->constantes) ? $c->constantes : [];
        $this->poids = $constantes['poids'] ?? '';
        $this->tension = $constantes['tension'] ?? '';
        $this->temperature = $constantes['temperature'] ?? '';
        $this->pouls = $constantes['pouls'] ?? '';
        $this->glycemie = $constantes['glycemie'] ?? '';

        $this->evaluations = is_array($c->evaluations) ? $c->evaluations : [];
        $this->visite_medicale_journaliere = is_array($c->visite_medicale_journaliere) ? $c->visite_medicale_journaliere : [];

        $this->searchPatient = '';
        $this->searchMedecin = '';

        $this->isEditMode = true;
        $this->isModalOpen = true;

        // Chargement des produits prescrits
        $this->produitsSelectionnes = [];
        if ($c->produits) {
            foreach ($c->produits as $prod) {
                $this->produitsSelectionnes[$prod->id] = [
                    'selected'            => true,
                    'quantite'            => $prod->pivot->quantite ?? 1,
                    'voie_administration' => $prod->pivot->voie_administration ?? '',
                    'posologie'           => $prod->pivot->posologie ?? '',
                    'prix_unitaire'       => $prod->pivot->prix ?? $prod->prix_vente ?? $prod->prix ?? 0,
                ];
            }
        }
    }

    public function calculerTotal()
    {
        $montantProduits = 0;

        foreach ($this->produitsSelectionnes as $produitId => $details) {
            if (!empty($details['selected'])) {
                $qte = (int) ($details['quantite'] ?? 1);
                $produit = \App\Models\produits::find($produitId);

                if ($produit) {
                    $pu = $produit->prix ?? 0; // Vérifiez si c'est bien 'prix' ou 'prix_vente'
                    $montantProduits += ($pu * $qte);
                }
            }
        }

        // Mettez à jour votre variable globale de total (ex: $this->montantTotal)
        $this->montantTotal = $montantProduits;
    }

    public function saveConsultation()
    {
        $validatedData = $this->validate();

        if (empty($this->consultation_id) && $this->isCreatingNewPatient) {
            $this->validate([
                'nouveau_nom' => 'required|string|max:255',
                'nouveau_telephone' => 'required|string|max:50',
            ]);

            $patientExistant = null;
            if (!empty($this->nouveau_telephone)) {
                $patientExistant = Patient::where('telephone', $this->nouveau_telephone)->first();
            }

            if ($patientExistant) {
                $this->patient_id = $patientExistant->id;
                session()->flash('info', "Ce patient existait déjà dans la base de données. Il a été associé automatiquement.");
            } else {
                $nouveauPatient = Patient::create([
                    'code_patient' => 'PAT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5)),
                    'uuid'         => (string) Str::uuid(),
                    'nom'          => $this->nouveau_nom,
                    'telephone'    => $this->nouveau_telephone,
                    'is_synced'    => false,
                ]);

                $this->patient_id = $nouveauPatient->id;
            }
        }

        if (!$this->dossier_medical_id && $this->patient_id) {
            $patient = Patient::find($this->patient_id);
            $this->dossier_medical_id = $patient?->dossier_medical_id ?? $patient?->dossierMedical?->id;
        }

        $dataToSave = [
            'patient_id'                    => $this->patient_id,
            'medecin_id'                    => $this->medecin_id,
            'dossier_medical_id'            => $this->dossier_medical_id,
            'date_heure_rdv'                => $this->date_heure_rdv,
            'type'                          => str_replace(' ', '_', strtolower(trim($this->type))),
            'prise_en_charge'               => str_replace(' ', '_', strtolower(trim($this->prise_en_charge))) ?: null,
            'statut'                        => $this->statut,
            'tarif_brut'                    => $this->tarif_brut,
            'motif'                         => $this->motif ?: null,
            'historique_maladie'            => $this->historique_maladie ?: null,
            'antecedents_maladie'           => $this->antecedents_maladie ?: null,
            'mode_de_vie'                   => $this->mode_de_vie ?: null,
            'terrain'                       => $this->terrain ?: null,
            'examen_physique'               => $this->examen_physique ?: null,
            'examen_general'                => $this->examen_general ?: null,
            'hypothese_diagnostique'        => $this->hypothese_diagnostique ?: null,
            'diagnostic'                    => $this->diagnostic ?: null,
            'resultats_analyses'            => $this->resultats_analyses ?: null,
            'resultats'                     => $this->resultats ?: null,
            'ordonnance'                    => $this->ordonnance ?: null,
            'traitement'                    => is_array($this->traitement) ? $this->traitement : (!empty($this->traitement) ? json_decode($this->traitement, true) : null),
            'traitement_sortie'             => $this->traitement_sortie ?: null,
            'evolution_maladie'             => $this->evolution_maladie ?: null,
            'notes_privees'                 => $this->notes_privees ?: null,
            'constantes'                    => [
                'poids'       => $this->poids,
                'tension'     => $this->tension,
                'temperature' => $this->temperature,
                'pouls'       => $this->pouls,
                'glycemie'    => $this->glycemie,
            ],
            'evaluations'                   => !empty($this->evaluations) ? $this->evaluations : null,
            'visite_medicale_journaliere' => !empty($this->visite_medicale_journaliere) ? $this->visite_medicale_journaliere : null,
            'bilan'                         => !empty($this->bilan) ? $this->bilan : null,
        ];

        $isEdit = !empty($this->consultation_id);

        if ($isEdit) {
            $ancienneConsultation = Consultation::find($this->consultation_id);
            $changements = [];

            $champsASuivre = [
                'motif'                       => 'Motif',
                'diagnostic'                  => 'Diagnostic',
                'ordonnance'                  => 'Ordonnance',
                'terrain'                     => 'Terrain',
                'tarif_brut'                  => 'Tarif Brut',
                'statut'                      => 'Statut',
                'type'                        => 'Type de consultation',
                'resultats'                   => 'Résultats',
                'historique_maladie'          => 'Historique de la maladie',
                'antecedents_maladie'         => 'Antécédents',
                'examen_physique'             => 'Examen physique',
                'evaluations'                 => 'Evaluations',
                'evolution_maladie'           => 'Evolution de la maladie',
                'visite_medicale_journaliere' => 'Visite médicale journalière',
                'constantes'                  => 'Constantes vitales',
            ];

            foreach ($champsASuivre as $champ => $libelle) {
                $valeurAncienne = $ancienneConsultation->$champ ?? '';
                $valeurNouvelle = $dataToSave[$champ] ?? '';

                if (is_array($valeurAncienne)) $valeurAncienne = json_encode($valeurAncienne, JSON_UNESCAPED_UNICODE);
                if (is_array($valeurNouvelle)) $valeurNouvelle = json_encode($valeurNouvelle, JSON_UNESCAPED_UNICODE);

                $strAncienne = (string) $valeurAncienne;
                $strNouvelle = (string) $valeurNouvelle;

                if ($strAncienne !== $strNouvelle) {
                    $changements[$champ] = [
                        'libelle' => $libelle,
                        'ancien'  => $strAncienne ?: '(Vide)',
                        'nouveau' => $strNouvelle ?: '(Vide)',
                    ];
                }
            }

            if (!empty($changements)) {
                $dataToSave['est_modifie'] = true;
                $dataToSave['vu_par_responsable'] = false;
                $dataToSave['modifications_historique'] = $changements;
            }
        }

        $consultation = Consultation::updateOrCreate(
            ['id' => $this->consultation_id],
            $dataToSave
        );

        // Synchronisation des produits et gestion des stocks
        $syncData = [];
        $montantProduits = 0;

        foreach ($this->produitsSelectionnes as $produitId => $details) {
            if (!empty($details['selected'])) {
                $qte = (int) ($details['quantite'] ?? 1);
                $produit = \App\Models\produits::find($produitId);

                if ($produit) {
                    if (!$isEdit) {
                        $produit->decrement('stock', $qte);
                    }

                    $pu = $produit->prix ?? $produit->prix_vente ?? 0;
                    $totalLigne = $pu * $qte;

                    $syncData[$produitId] = [
                        'quantite'            => $qte,
                        'voie_administration' => $details['voie_administration'] ?? null,
                        'posologie'           => $details['posologie'] ?? null,
                        'prix_unitaire'       => $pu,
                    ];

                    $montantProduits += $totalLigne;
                }
            }
        }

        // Détermination du statut de la commande (Terminé + Payé)
        $estTermine = (strtolower($consultation->statut ?? '') === 'termine' || strtolower($consultation->statut ?? '') === 'terminé');
        $estPaye = ($consultation->est_paye ?? false) === true || in_array(strtolower($consultation->statut_paiement ?? ''), ['paye', 'payé', 'paid']);
        $statutCommande = ($estTermine && $estPaye) ? 'paye' : 'en_attente';

        // Gestion de la commande en arrière-plan
        $commande = \App\Models\commandes::where('consultation_id', $consultation->id)->first();

        if ($montantProduits > 0) {
            if (!$commande) {
                // Création de la commande avec le bon statut dès le départ
                $commande = \App\Models\commandes::create([
                    'consultation_id' => $consultation->id,
                    'client_id'       => $consultation->patient_id,
                    'reference'       => 'SWB-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                    'user_id'         => auth()->id(),
                    'nom'             => $consultation->patient?->nom,
                    'phone'           => $consultation->patient?->telephone,
                    'montant_total'   => $montantProduits,
                    'statut'          => $statutCommande,
                ]);
            } else {
                // Mise à jour de la commande existante
                $commande->update([
                    'montant_total'   => $montantProduits,
                    'statut'          => $statutCommande,
                ]);
            }

            // Nettoyage et recréation des lignes de contenu de commande
            \App\Models\contenu_commande::where('id_commande', $commande->id)->delete();

            foreach ($syncData as $produitId => $info) {
                $totalLigne = $info['prix_unitaire'] * $info['quantite'];
                \App\Models\contenu_commande::create([
                    'id_commande'   => $commande->id,
                    'id_produit'    => $produitId,
                    'quantite'      => $info['quantite'],
                    'quantity'      => $info['quantite'],
                    'prix_unitaire' => $info['prix_unitaire'],
                    'prix'          => $totalLigne,
                ]);
            }
        } else {
            // S'il n'y a plus de produits sélectionnés, on supprime la commande et ses lignes
            if ($commande) {
                \App\Models\contenu_commande::where('id_commande', $commande->id)->delete();
                $commande->delete();
            }
        }

        // 2. Synchronisation avec la consultation
        $consultation->produits()->sync($syncData);

        // Notification
        $nomPatient = $consultation->patient?->nom_complet
            ?? trim(($consultation->patient?->nom ?? '') . ' ' . ($consultation->patient?->prenom ?? ''))
            ?? 'Patient';

        $notification = new notifications();
        $notification->url = '#';
        if ($isEdit) {
            $notification->titre = "Consultation modifiée";
            $notification->message = "Consultation de {$nomPatient} mise à jour.";
            $notification->type = "consultation_modification";
        } else {
            $notification->titre = "Nouvelle consultation";
            $notification->message = "Enregistrée pour {$nomPatient}.";
            $notification->type = "consultation_creation";
        }
        $notification->statut = "unread";
        $notification->save();

        $this->selectedConsultationId = $consultation->id;

        $this->reset([
            'patient_id',
            'searchPatient',
            'isCreatingNewPatient',
            'nouveau_nom',
            'nouveau_prenom',
            'nouveau_telephone',
            'nouveau_genre',
            'nouveau_date_naissance',
            'nouveau_groupe_sanguin',
            'produitsSelectionnes',
            'searchProduit',
        ]);
if (method_exists($consultation, 'produits')) {
            $consultation->produits()->sync($syncData);
        }
        session()->flash('message', $isEdit ? 'Consultation mise à jour avec succès.' : 'Consultation enregistrée avec succès.');
        $this->closeModal();
    }

    public function saveConsultation2()
    {
        $validatedData = $this->validate();

        if (empty($this->consultation_id) && $this->isCreatingNewPatient) {
            $this->validate([
                'nouveau_nom' => 'required|string|max:255',
                'nouveau_telephone' => 'required|string|max:50',
            ]);

            $patientExistant = null;
            if (!empty($this->nouveau_telephone)) {
                $patientExistant = Patient::where('telephone', $this->nouveau_telephone)->first();
            }

            if ($patientExistant) {
                $this->patient_id = $patientExistant->id;
                session()->flash('info', "Ce patient existait déjà dans la base de données. Il a été associé automatiquement.");
            } else {
                $nouveauPatient = Patient::create([
                    'code_patient' => 'PAT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5)),
                    'uuid'         => (string) Str::uuid(),
                    'nom'          => $this->nouveau_nom,
                    'telephone'    => $this->nouveau_telephone,
                    'is_synced'    => false,
                ]);

                $this->patient_id = $nouveauPatient->id;
            }
        }

        if (!$this->dossier_medical_id && $this->patient_id) {
            $patient = Patient::find($this->patient_id);
            $this->dossier_medical_id = $patient?->dossier_medical_id ?? $patient?->dossierMedical?->id;
        }

        $dataToSave = [
            'patient_id'                  => $this->patient_id,
            'medecin_id'                  => $this->medecin_id,
            'dossier_medical_id'          => $this->dossier_medical_id,
            'date_heure_rdv'              => $this->date_heure_rdv,
            'type'                        => str_replace(' ', '_', strtolower(trim($this->type))),
            'prise_en_charge'             => str_replace(' ', '_', strtolower(trim($this->prise_en_charge))) ?: null,
            'statut'                      => $this->statut,
            'tarif_brut'                  => $this->tarif_brut,
            'motif'                       => $this->motif ?: null,
            'historique_maladie'          => $this->historique_maladie ?: null,
            'antecedents_maladie'         => $this->antecedents_maladie ?: null,
            'mode_de_vie'                 => $this->mode_de_vie ?: null,
            'terrain'                     => $this->terrain ?: null,
            'examen_physique'             => $this->examen_physique ?: null,
            'examen_general'              => $this->examen_general ?: null,
            'hypothese_diagnostique'      => $this->hypothese_diagnostique ?: null,
            'diagnostic'                  => $this->diagnostic ?: null,
            'resultats_analyses'          => $this->resultats_analyses ?: null,
            'resultats'                   => $this->resultats ?: null,
            'ordonnance'                  => $this->ordonnance ?: null,
            'traitement'                  => is_array($this->traitement) ? $this->traitement : (!empty($this->traitement) ? json_decode($this->traitement, true) : null),
            'traitement_sortie'           => $this->traitement_sortie ?: null,
            'evolution_maladie'           => $this->evolution_maladie ?: null,
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
            'bilan'                       => !empty($this->bilan) ? $this->bilan : null,
        ];

        $isEdit = !empty($this->consultation_id);

        if ($isEdit) {
            $ancienneConsultation = Consultation::find($this->consultation_id);
            $changements = [];

            $champsASuivre = [
                'motif'                       => 'Motif',
                'diagnostic'                  => 'Diagnostic',
                'ordonnance'                  => 'Ordonnance',
                'terrain'                     => 'Terrain',
                'tarif_brut'                  => 'Tarif Brut',
                'statut'                      => 'Statut',
                'type'                        => 'Type de consultation',
                'resultats'                   => 'Résultats',
                'historique_maladie'          => 'Historique de la maladie',
                'antecedents_maladie'         => 'Antécédents',
                'examen_physique'             => 'Examen physique',
                'evaluations'                 => 'Evaluations',
                'evolution_maladie'           => 'Evolution de la maladie',
                'visite_medicale_journaliere' => 'Visite médicale journalière',
                'constantes'                  => 'Constantes vitales',
            ];

            foreach ($champsASuivre as $champ => $libelle) {
                $valeurAncienne = $ancienneConsultation->$champ ?? '';
                $valeurNouvelle = $dataToSave[$champ] ?? '';

                if (is_array($valeurAncienne)) $valeurAncienne = json_encode($valeurAncienne, JSON_UNESCAPED_UNICODE);
                if (is_array($valeurNouvelle)) $valeurNouvelle = json_encode($valeurNouvelle, JSON_UNESCAPED_UNICODE);

                $strAncienne = (string) $valeurAncienne;
                $strNouvelle = (string) $valeurNouvelle;

                if ($strAncienne !== $strNouvelle) {
                    $changements[$champ] = [
                        'libelle' => $libelle,
                        'ancien'  => $strAncienne ?: '(Vide)',
                        'nouveau' => $strNouvelle ?: '(Vide)',
                    ];
                }
            }

            if (!empty($changements)) {
                $dataToSave['est_modifie'] = true;
                $dataToSave['vu_par_responsable'] = false;
                $dataToSave['modifications_historique'] = $changements;
            }
        }

        $consultation = Consultation::updateOrCreate(
            ['id' => $this->consultation_id],
            $dataToSave
        );

        // Synchronisation des produits et gestion des stocks
        $syncData = [];
        $montantProduits = 0;



        // 1. D'abord, on nettoie ou supprime les anciens contenus de commande s'il y en a pour cette consultation
        // (En supposant que votre modèle 'commandes' a une relation 'consultation' ou 'consultation_id')
        $commande = \App\Models\commandes::where('consultation_id', $consultation->id)->first();

        if (!$commande && !empty(array_filter($this->produitsSelectionnes, fn($d) => !empty($d['selected'])))) {
            // Création de la commande si elle n'existe pas et qu'il y a des produits
            $commande = \App\Models\commandes::create([
                'consultation_id' => $consultation->id,
                'client_id'       => $consultation->patient_id,
                'reference'       => 'SWB-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id'         => auth()->id(),
                'nom'             => $consultation->patient?->nom,
                'phone'           => $consultation->patient?->telephone,
                'statut'          => 'en_attente',
            ]);
        }

        // Si la commande existe, on vide ses anciens contenus pour les recadrer proprement
        if ($commande) {
            \App\Models\contenu_commande::where('id_commande', $commande->id)->delete();
        }

        foreach ($this->produitsSelectionnes as $produitId => $details) {
            if (!empty($details['selected'])) {
                $qte = (int) ($details['quantite'] ?? 1);
                $produit = \App\Models\produits::find($produitId);

                if ($produit) {
                    if (!$isEdit) {
                        $produit->decrement('stock', $qte);
                    }

                    // Récupération du prix (selon votre structure, on prend 'prix' ou 'prix_vente')
                    $pu = $produit->prix ?? $produit->prix_vente ?? 0;
                    $totalLigne = $pu * $qte;

                    // Données pour la table pivot de la consultation
                    $syncData[$produitId] = [
                        'quantite'            => $qte,
                        'voie_administration' => $details['voie_administration'] ?? null,
                        'posologie'           => $details['posologie'] ?? null,
                        'prix_unitaire'       => $pu,
                    ];

                    // Enregistrement dans 'contenu_commandes' (basé sur votre modèle)
                    if ($commande) {
                        \App\Models\contenu_commande::create([
                            'id_commande'   => $commande->id,
                            'id_produit'    => $produitId,
                            'quantite'      => $qte,
                            'quantity'      => $qte, // Pour combler les doublons de colonnes de votre modèle
                            'prix_unitaire' => $pu,
                            'prix'          => $totalLigne,
                        ]);
                    }

                    $montantProduits += $totalLigne;
                }
            }
        }

        // 2. Synchronisation avec la consultation
        $consultation->produits()->sync($syncData);

        // 3. Mise à jour finale du montant total de la commande
        if ($commande) {
            $statutCommande = ($consultation->statut === 'termine' && ($consultation->est_paye || $consultation->statut_paiement === 'paye')) ? 'paye' : 'en_attente';

            if ($montantProduits > 0) {
                $commande->update([
                    'montant_total' => $montantProduits,
                    'statut'        => $statutCommande,
                ]);
            } else {
                // S'il n'y a plus de produits, on supprime la commande
                $commande->delete();
            }
        }
        // Notification
        $nomPatient = $consultation->patient?->nom_complet
            ?? trim(($consultation->patient?->nom ?? '') . ' ' . ($consultation->patient?->prenom ?? ''))
            ?? 'Patient';

        $notification = new notifications();
        $notification->url = '#';
        if ($isEdit) {
            $notification->titre = "Consultation modifiée";
            $notification->message = "Consultation de {$nomPatient} mise à jour.";
            $notification->type = "consultation_modification";
        } else {
            $notification->titre = "Nouvelle consultation";
            $notification->message = "Enregistrée pour {$nomPatient}.";
            $notification->type = "consultation_creation";
        }
        $notification->statut = "unread";
        $notification->save();

        $this->selectedConsultationId = $consultation->id;

        $this->reset([
            'patient_id',
            'searchPatient',
            'isCreatingNewPatient',
            'nouveau_nom',
            'nouveau_prenom',
            'nouveau_telephone',
            'nouveau_genre',
            'nouveau_date_naissance',
            'nouveau_groupe_sanguin',
            'produitsSelectionnes',
            'searchProduit',
        ]);

        session()->flash('message', $isEdit ? 'Consultation mise à jour avec succès.' : 'Consultation enregistrée avec succès.');
        $this->closeModal();
    }

    public function showConsultation($id)
    {
        $this->selectedConsultation = Consultation::with([
            'patient.assurance',
            'medecin',
            'demandesExamens.examen',
            'produits'
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
            ->with(['patient.assurance', 'medecin', 'demandesExamens', 'produits'])
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('code_patient', 'like', '%' . $this->search . '%');
                })->orWhere('code_consultation', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtreStatut, function ($query) {
                $query->where('statut', $this->filtreStatut);
            })
            ->when($this->filtreDate, function ($query) {
                $query->whereDate('date_heure_rdv', $this->filtreDate);
            })
            ->when($this->filtrePaiement !== '', function ($query) {
                if ($this->filtrePaiement == '0') {
                    $query->where('est_paye', false);
                } else {
                    $query->where('est_paye', true);
                }
            })
            ->when($this->filtreModificationsAlerte, function ($query) {
                $query->where('est_modifie', true)->where('vu_par_responsable', false);
            })
            ->latest('date_heure_rdv')
            ->paginate(10);

        $this->typeConsultations = $this->getTypeConsultations();

        // Récupération dynamique des patients selon la recherche dans la modale
        $patients = !empty($this->searchPatient)
            ? Patient::where('nom', 'like', '%' . $this->searchPatient . '%')
                ->orWhere('prenom', 'like', '%' . $this->searchPatient . '%')
                ->orWhere('telephone', 'like', '%' . $this->searchPatient . '%')
                ->limit(10)
                ->get()
            : Patient::take(10)->get();

        // Récupération dynamique des médecins
        $medecins = User::when(!empty($this->searchMedecin), function ($q) {
            $q->where('nom', 'like', '%' . $this->searchMedecin . '%')
              ->orWhere('prenom', 'like', '%' . $this->searchMedecin . '%')
              ->orWhere('email', 'like', '%' . $this->searchMedecin . '%')
                ->orWhere('telephone', 'like', '%' . $this->searchMedecin . '%');

        })->get();

        // Récupération des produits pour la prescription dans la modale
        $produits = [];
        if (class_exists(\App\Models\produits::class)) {
            $produits = \App\Models\produits::when(!empty($this->searchProduit), function ($q) {
                $q->where('nom', 'like', '%' . $this->searchProduit . '%')
                  ->orWhere('description', 'like', '%' . $this->searchProduit . '%');
            })->limit(10)->get();
        }

        return view('livewire.consultations.gestion-consultations', [
            'consultations' => $consultations,
            'patients'      => $patients,
            'medecins'      => $medecins,
            'produits'      => $produits,
        ]);
    }
}
