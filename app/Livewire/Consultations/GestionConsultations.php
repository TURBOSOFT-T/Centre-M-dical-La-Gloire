<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class GestionConsultations extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtres généraux du tableau
    public $search = '';
    public $filtreStatut = '';
    public $filtreDate = '';
    public $filtrePaiement = ''; // '' = Tous, '0' = Impayés, '1' = Payés

    // Recherche dynamique spécifique au patient dans le Modal
    public $searchPatient = '';

    // Formulaire Consultation / RDV
    public $consultation_id;
    public $patient_id;
    public $medecin_id;
    public $date_heure_rdv;
    public $type = 'consultation_generale';
    public $statut = 'programme';
    public $motif;
    public $examen_physique;
    public $diagnostic;
    public $ordonnance;
    public $notes_privees;
    public $tarif_brut = 5000; // Tarif standard
    public $est_paye = false;

    // Constantes lors du RDV
    public $poids, $tension, $temperature, $pouls;

    // Modales
    public $isModalOpen = false;
    public $isEditMode = false;
    public $selectedConsultation = null;
    public $isViewModalOpen = false;

    protected function rules()
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'nullable|exists:users,id',
            'date_heure_rdv' => 'required|date',
            'type' => 'required|in:consultation_generale,specialiste,suivi,urgence',
            'statut' => 'required|in:programme,en_attente,en_cours,termine,annule',
            'motif' => 'nullable|string',
            'examen_physique' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'ordonnance' => 'nullable|string',
            'notes_privees' => 'nullable|string',
            'tarif_brut' => 'required|numeric|min:0',
            'est_paye' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->date_heure_rdv = date('Y-m-d\TH:i');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFiltreStatut() { $this->resetPage(); }
    public function updatingFiltreDate() { $this->resetPage(); }
    public function updatingFiltrePaiement() { $this->resetPage(); }

    /**
     * Sélectionne le patient dans la liste de recherche dynamique
     * et auto-complète ses dernières constantes connues
     */
    public function selectPatient($id)
    {
        $this->patient_id = $id;

        $patient = Patient::find($id);
        if ($patient && !empty($patient->parametres)) {
            $params = $patient->parametres;
            $this->poids = $params['poids'] ?? '';
            $this->tension = $params['tension'] ?? '';
            $this->temperature = $params['temperature'] ?? '';
            $this->pouls = $params['pouls'] ?? '';
        }
    }

    /**
     * Action rapide Caisse : Filtre les consultations impayées
     */
    public function filtrerEnAttentePaiement()
    {
        $this->resetPage();
        $this->filtrePaiement = '0';
        $this->filtreStatut = '';
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
        $this->patient_id = null;
        $this->searchPatient = ''; // Réinitialiser le champ de recherche
        $this->medecin_id = auth()->id();
        $this->date_heure_rdv = date('Y-m-d\TH:i');
        $this->type = 'consultation_generale';
        $this->statut = 'programme';
        $this->motif = '';
        $this->examen_physique = '';
        $this->diagnostic = '';
        $this->ordonnance = '';
        $this->notes_privees = '';
        $this->tarif_brut = 5000;
        $this->est_paye = false;
        $this->poids = '';
        $this->tension = '';
        $this->temperature = '';
        $this->pouls = '';
        $this->resetValidation();
    }

    public function saveConsultation()
    {
        $validatedData = $this->validate();

        // Conversion explicite des chaînes vides pour éviter les exceptions SQL
        $validatedData['motif'] = $this->motif ?: null;
        $validatedData['examen_physique'] = $this->examen_physique ?: null;
        $validatedData['diagnostic'] = $this->diagnostic ?: null;
        $validatedData['ordonnance'] = $this->ordonnance ?: null;
        $validatedData['notes_privees'] = $this->notes_privees ?: null;

        $validatedData['constantes'] = [
            'poids' => $this->poids,
            'tension' => $this->tension,
            'temperature' => $this->temperature,
            'pouls' => $this->pouls,
        ];

        Consultation::updateOrCreate(['id' => $this->consultation_id], $validatedData);

        session()->flash('message', $this->isEditMode ? 'Rendez-vous mis à jour.' : 'Rendez-vous / Consultation enregistré(e).');
        $this->closeModal();
    }

    public function editConsultation($id)
    {
        $c = Consultation::findOrFail($id);
        $this->consultation_id = $c->id;
        $this->patient_id = $c->patient_id;
        $this->medecin_id = $c->medecin_id;
        $this->date_heure_rdv = $c->date_heure_rdv->format('Y-m-d\TH:i');
        $this->type = $c->type;
        $this->statut = $c->statut;
        $this->motif = $c->motif;
        $this->examen_physique = $c->examen_physique;
        $this->diagnostic = $c->diagnostic;
        $this->ordonnance = $c->ordonnance;
        $this->notes_privees = $c->notes_privees;
        $this->tarif_brut = $c->tarif_brut;
        $this->est_paye = (bool) $c->est_paye;

        // Si en mode édition, réinitialiser la recherche
        $this->searchPatient = '';

        $constantes = $c->constantes ?? [];
        $this->poids = $constantes['poids'] ?? '';
        $this->tension = $constantes['tension'] ?? '';
        $this->temperature = $constantes['temperature'] ?? '';
        $this->pouls = $constantes['pouls'] ?? '';

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function showConsultation($id)
    {
        $this->selectedConsultation = Consultation::with(['patient.assurance', 'medecin'])->findOrFail($id);
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

    public function render()
    {
        // 1. Consultation avec filtres
        $consultations = Consultation::query()
            ->with(['patient.assurance', 'medecin'])
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

        // 2. Recherche dynamique des patients pour le modal (Limité à 10 résultats)
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

        // Compteur de consultations en attente de paiement
        $countEnAttentePaiement = Consultation::where('est_paye', false)->count();

        $medecins = User::orderBy('nom', 'asc')->get();

        return view('livewire.consultations.gestion-consultations', compact(
            'consultations',
            'patients',
            'medecins',
            'countEnAttentePaiement'
        ));
    }
}