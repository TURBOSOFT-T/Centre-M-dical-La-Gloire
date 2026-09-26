<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Hospitalisation;
use App\Models\DossierMedical;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HospitalisationManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Navigation & Filtres
    public $mode = 'index'; // 'index', 'create', 'show', 'liberer'
    public $search = '';
    public $filterStatut = '';

    // Champs Formulaire Admission
    public $hospitalisation_id;
    public $dossier_medical_id;
    public $patient_id;
    public $medecin_id;
    public $chambre_number = '';
    public $lit_number = '';
    public $service_department = '';
    public $date_entree;
    public $date_sortie_prevue;
    public $motif_admission = '';
    public $diagnostic_entree = '';
    public $tarif_journalier = 0;
    public $frais_soins_chambre = 0;
    public $part_assurance = 0;

    // Recherche de patient / dossier
    public $searchPatient = '';
    public $selectedPatientName = '';

    // Champs Formulaire Sortie / Libération
    public $date_sortie_effective;
    public $diagnostic_sortie = '';
    public $observations = '';
    public $montant_paye = 0;

    // Hospitalisation sélectionnée pour consultation/édition
    public $selectedHospitalisation;

    public function mount()
    {
        $this->date_entree = now()->format('Y-m-d\TH:i');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatut()
    {
        $this->resetPage();
    }

    public function render()
    {
        $hospitalisations = Hospitalisation::with(['patient', 'dossierMedical', 'medecin'])
            ->when($this->search, function ($q) {
                $q->where('code_hospitalisation', 'like', '%' . $this->search . '%')
                  ->orWhere('chambre_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('patient', function ($qp) {
                      $qp->where('nom', 'like', '%' . $this->search . '%')
                         ->orWhere('prenom', 'like', '%' . $this->search . '%')
                         ->orWhere('telephone', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->filterStatut, function ($q) {
                $q->where('statut', $this->filterStatut);
            })
            ->latest('date_entree')
            ->paginate(10);

        // Recherche dynamique de tous les patients (avec ou sans dossier médical)
        $patientsFound = [];
        if (!empty($this->searchPatient) && !$this->selectedPatientName) {
            $patientsFound = Patient::where('nom', 'like', '%' . $this->searchPatient . '%')
                ->orWhere('prenom', 'like', '%' . $this->searchPatient . '%')
                ->orWhere('telephone', 'like', '%' . $this->searchPatient . '%')
                ->with('dossierMedical')
                ->take(5)
                ->get();
        }

        $medecins = User::all();

        return view('livewire.hospitalisation-manager', [
            'hospitalisations' => $hospitalisations,
            'patientsFound' => $patientsFound,
            'medecins' => $medecins,
        ]);
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->date_entree = now()->format('Y-m-d\TH:i');
        $this->mode = 'create';
    }

    public function selectPatient($patientId, $patientName, $dossierId = null)
    {
        $this->patient_id = $patientId;
        $this->selectedPatientName = $patientName;
        $this->searchPatient = '';

        // Si le dossier médical existe déjà, on l'associe directement
        if ($dossierId) {
            $this->dossier_medical_id = $dossierId;
        } else {
            // Création automatique conforme aux champs de la migration dossiers_medicaux
            $patient = Patient::with('dossierMedical')->find($patientId);

            if ($patient->dossierMedical) {
                $this->dossier_medical_id = $patient->dossierMedical->id;
            } else {
                $codeDossier = 'DM-' . date('Y') . '-' . str_pad(DossierMedical::count() + 1, 4, '0', STR_PAD_LEFT);
                
                $dossier = DossierMedical::create([
                    'patient_id' => $patient->id,
                    'code_dossier' => $codeDossier,
                    'statut' => 'actif',
                ]);
                
                $this->dossier_medical_id = $dossier->id;
            }
        }
    }

    public function clearPatient()
    {
        $this->patient_id = null;
        $this->dossier_medical_id = null;
        $this->selectedPatientName = '';
        $this->searchPatient = '';
    }

    public function store()
    {
        $this->validate([
            'patient_id' => 'required|exists:patients,id',
            'dossier_medical_id' => 'required|exists:dossiers_medicaux,id',
            'chambre_number' => 'required|string|max:50',
            'date_entree' => 'required|date',
            'motif_admission' => 'required|string',
            'tarif_journalier' => 'required|numeric|min:0',
        ], [
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'dossier_medical_id.required' => 'Le dossier médical du patient est introuvable.',
            'chambre_number.required' => 'Le numéro de chambre est obligatoire.',
            'date_entree.required' => 'La date d\'entrée est obligatoire.',
            'motif_admission.required' => 'Le motif d\'admission est obligatoire.',
        ]);

        $code = 'HOSP-' . date('Y') . '-' . str_pad(Hospitalisation::count() + 1, 4, '0', STR_PAD_LEFT);

        Hospitalisation::create([
            'code_hospitalisation' => $code,
            'dossier_medical_id' => $this->dossier_medical_id,
            'patient_id' => $this->patient_id,
            'medecin_id' => $this->medecin_id ?: null,
            'agent_id' => Auth::id(),
            'chambre_number' => $this->chambre_number,
            'lit_number' => $this->lit_number,
            'service_department' => $this->service_department,
            'date_entree' => $this->date_entree,
            'date_sortie_prevue' => $this->date_sortie_prevue ?: null,
            'motif_admission' => $this->motif_admission,
            'diagnostic_entree' => $this->diagnostic_entree,
            'tarif_journalier' => $this->tarif_journalier,
            'frais_soins_chambre' => $this->frais_soins_chambre,
            'part_assurance' => $this->part_assurance,
            'statut' => 'en_cours',
        ]);

        session()->flash('message', 'Hospitalisation enregistrée avec succès (Code : ' . $code . ').');
        $this->backToIndex();
    }

    public function openShow($id)
    {
        $this->selectedHospitalisation = Hospitalisation::with(['patient', 'dossierMedical', 'medecin', 'agent'])->findOrFail($id);
        $this->mode = 'show';
    }

    public function openLiberer($id)
    {
        $this->selectedHospitalisation = Hospitalisation::findOrFail($id);
        $this->date_sortie_effective = now()->format('Y-m-d\TH:i');
        $this->mode = 'liberer';
    }

    public function enregistrerSortie()
    {
        $this->validate([
            'date_sortie_effective' => 'required|date',
            'diagnostic_sortie' => 'nullable|string',
        ]);

        $hosp = $this->selectedHospitalisation;

        $hosp->date_sortie_effective = $this->date_sortie_effective;
        $hosp->diagnostic_sortie = $this->diagnostic_sortie;
        $hosp->observations = $this->observations;
        $hosp->statut = 'libere';

        // Recalcul financier
        $total = $hosp->calculerMontantTotal();
        $partPatient = max(0, $total - $hosp->part_assurance);

        $hosp->montant_total = $total;
        $hosp->part_patient = $partPatient;
        $hosp->montant_paye = $this->montant_paye;

        if ($this->montant_paye >= $partPatient) {
            $hosp->statut_paiement = 'paye';
        } elseif ($this->montant_paye > 0) {
            $hosp->statut_paiement = 'partiel';
        } else {
            $hosp->statut_paiement = 'non_paye';
        }

        $hosp->save();

        session()->flash('message', 'La sortie du patient a été enregistrée avec succès.');
        $this->backToIndex();
    }

    public function backToIndex()
    {
        $this->resetForm();
        $this->mode = 'index';
    }

    private function resetForm()
    {
        $this->reset([
            'hospitalisation_id', 'dossier_medical_id', 'patient_id', 'medecin_id',
            'chambre_number', 'lit_number', 'service_department', 'date_entree',
            'date_sortie_prevue', 'motif_admission', 'diagnostic_entree', 'tarif_journalier',
            'frais_soins_chambre', 'part_assurance', 'searchPatient', 'selectedPatientName',
            'date_sortie_effective', 'diagnostic_sortie', 'observations', 'montant_paye',
            'selectedHospitalisation'
        ]);
    }
}