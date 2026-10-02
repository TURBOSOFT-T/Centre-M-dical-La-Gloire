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
    
    public $type_prise_en_charge = 'hospitalisation'; // 'observation' ou 'hospitalisation'
    public $standing_type = 'classique'; // 'haut_standing', 'classique', 'economique', 'post_op'

    public $chambre_number = '';
    public $lit_number = '';
    public $service_department = '';
    public $date_entree;
    public $date_sortie_prevue;
    public $nombre_jours = 1; // Ajout du nombre de jours
    public $motif_admission = '';
    public $diagnostic_entree = '';
    public $tarif_journalier = 0;
    public $frais_soins_chambre = 0;
    public $part_assurance = 0;

    // Recherche de patient / dossier
    public $searchPatient = '';
    public $selectedPatientName = '';

    // Champs Formulaire Sortie / Libération & Avances
    public $date_sortie_effective;
    public $diagnostic_sortie = '';
    public $observations = '';
    public $montant_paye = 0;
    public $montant_verse = 0; // Pour enregistrer une avance intermédiaire

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

    public function updatedTypePriseEnCharge($value)
    {
        if ($value === 'observation') {
            $this->chambre_number = '';
            $this->lit_number = '';
            $this->date_entree = null;
            $this->nombre_jours = 1;
        } else {
            $this->date_entree = now()->format('Y-m-d\TH:i');
        }
    }

    // Gestion du calcul automatique des jours en fonction des dates
    public function updatedDateEntree()
    {
        $this->calculerJours();
    }

    public function updatedDateSortiePrevue()
    {
        $this->calculerJours();
    }

    public function calculerJours()
    {
        if ($this->type_prise_en_charge === 'hospitalisation' && $this->date_entree && $this->date_sortie_prevue) {
            $entree = \Carbon\Carbon::parse($this->date_entree);
            $sortie = \Carbon\Carbon::parse($this->date_sortie_prevue);
            
            if ($sortie->greaterThan($entree)) {
                $this->nombre_jours = max(1, $entree->diffInDays($sortie));
            }
        }
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
            ->latest('created_at')
            ->paginate(10);

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

        if ($dossierId) {
            $this->dossier_medical_id = $dossierId;
        } else {
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
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'dossier_medical_id' => 'required|exists:dossiers_medicaux,id',
            'type_prise_en_charge' => 'required|in:observation,hospitalisation',
            'motif_admission' => 'required|string',
            'tarif_journalier' => 'required|numeric|min:0',
        ];

        if ($this->type_prise_en_charge === 'hospitalisation') {
            $rules['chambre_number'] = 'required|string|max:50';
            $rules['date_entree'] = 'required|date';
            $rules['standing_type'] = 'required|in:haut_standing,classique,economique,post_op';
            $rules['nombre_jours'] = 'required|integer|min:1';
        }

        $this->validate($rules, [
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'dossier_medical_id.required' => 'Le dossier médical du patient est introuvable.',
            'chambre_number.required' => 'Le numéro de chambre est obligatoire pour une hospitalisation.',
            'date_entree.required' => 'La date d\'entrée est obligatoire.',
            'motif_admission.required' => 'Le motif d\'admission est obligatoire.',
            'standing_type.required' => 'Veuillez sélectionner le type de standing.',
            'nombre_jours.min' => 'Le nombre de jours doit être d\'au moins 1.',
        ]);

        $code = 'HOSP-' . date('Y') . '-' . str_pad(Hospitalisation::count() + 1, 4, '0', STR_PAD_LEFT);

        $dateSortiePrevue = $this->date_sortie_prevue;
        if (!$dateSortiePrevue && $this->type_prise_en_charge === 'hospitalisation' && $this->date_entree) {
            $dateSortiePrevue = \Carbon\Carbon::parse($this->date_entree)->addDays(intval($this->nombre_jours));
        }

        $hosp = Hospitalisation::create([
            'code_hospitalisation' => $code,
            'dossier_medical_id' => $this->dossier_medical_id,
            'patient_id' => $this->patient_id,
            'medecin_id' => $this->medecin_id ?: null,
            'agent_id' => Auth::id(),
            'type_prise_en_charge' => $this->type_prise_en_charge,
            'standing_type' => $this->type_prise_en_charge === 'hospitalisation' ? $this->standing_type : null,
            'chambre_number' => $this->chambre_number ?: null,
            'lit_number' => $this->lit_number ?: null,
            'service_department' => $this->service_department ?: null,
            'date_entree' => $this->date_entree ?: null,
            'date_sortie_prevue' => $dateSortiePrevue ?: null,
            'motif_admission' => $this->motif_admission,
            'diagnostic_entree' => $this->diagnostic_entree,
            'tarif_journalier' => floatval($this->tarif_journalier),
            'frais_soins_chambre' => floatval($this->frais_soins_chambre),
            'part_assurance' => floatval($this->part_assurance),
            'statut' => 'en_cours',
        ]);

        // Conversion sécurisée en types numériques pour éviter l'erreur de types
        $tarif = floatval($this->tarif_journalier);
        $jours = intval($this->nombre_jours);
        $frais = floatval($this->frais_soins_chambre);
        $assurance = floatval($this->part_assurance);

        $total = ($this->type_prise_en_charge === 'hospitalisation') 
            ? ($tarif * $jours) + $frais 
            : $tarif + $frais;

        $hosp->montant_total = $total;
        $hosp->part_patient = max(0, $total - $assurance);
        $hosp->save();

        session()->flash('message', 'Prise en charge enregistrée avec succès (Code : ' . $code . ').');
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
    }public function enregistrerAvance($id)
    {
        $hosp = Hospitalisation::findOrFail($id);
        
        // Utiliser directement le montant total stocké en base de données
        $total = floatval($hosp->montant_total);
        $partPatient = max(0, $total - floatval($hosp->part_assurance));
        $resteAPayer = max(0, $partPatient - floatval($hosp->montant_paye));

        $this->validate([
            'montant_verse' => 'required|numeric|min:1|max:' . $resteAPayer,
        ], [
            'montant_verse.required' => 'Veuillez saisir un montant valide.',
            'montant_verse.min' => 'Le montant doit être supérieur à 0.',
            'montant_verse.max' => 'L\'avance ne peut pas dépasser le reste à payer (' . number_format($resteAPayer, 0, ',', ' ') . ' FCFA).',
        ]);

        $hosp->montant_paye = floatval($hosp->montant_paye) + floatval($this->montant_verse);
        $hosp->part_patient = $partPatient;

        if ($hosp->montant_paye >= $partPatient) {
            $hosp->statut_paiement = 'paye';
        } elseif ($hosp->montant_paye > 0) {
            $hosp->statut_paiement = 'partiel';
        } else {
            $hosp->statut_paiement = 'non_paye';
        }

        $hosp->save();

        $this->montant_verse = 0;
        session()->flash('message', 'Avance de paiement enregistrée avec succès.');
        
        $this->selectedHospitalisation = $hosp->fresh(['patient', 'dossierMedical', 'medecin', 'agent']);
    }

    public function enregistrerSortie()
    {
        $hosp = $this->selectedHospitalisation;

        // Utiliser directement le montant total stocké en base de données
        $total = floatval($hosp->montant_total);
        $partPatient = max(0, $total - floatval($hosp->part_assurance));
        $resteAPayer = max(0, $partPatient - floatval($hosp->montant_paye));

        $this->validate([
            'date_sortie_effective' => 'nullable|date',
            'diagnostic_sortie' => 'nullable|string',
            'montant_paye' => 'nullable|numeric|min:0|max:' . $resteAPayer,
        ], [
            'montant_paye.max' => 'Le montant réglé ne peut pas dépasser le reste à payer (' . number_format($resteAPayer, 0, ',', ' ') . ' FCFA).',
        ]);

        if ($hosp->type_prise_en_charge === 'hospitalisation') {
            $hosp->date_sortie_effective = $this->date_sortie_effective ?: now();
        }

        $hosp->diagnostic_sortie = $this->diagnostic_sortie;
     $hosp->observations = $this->observations;
        $hosp->statut = 'libere';

        $hosp->montant_total = $total;
        $hosp->part_patient = $partPatient;
        
        $montantSortie = floatval($this->montant_paye ?? 0);
        if ($montantSortie > 0) {
            $hosp->montant_paye = floatval($hosp->montant_paye) + $montantSortie;
        }

        if ($hosp->montant_paye >= $partPatient) {
            $hosp->statut_paiement = 'paye';
        } elseif ($hosp->montant_paye > 0) {
            $hosp->statut_paiement = 'partiel';
        } else {
            $hosp->statut_paiement = 'non_paye';
        }

        $hosp->save();

        session()->flash('message', 'La sortie / libération du patient a été enregistrée avec succès.');
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
            'type_prise_en_charge', 'standing_type', 'chambre_number', 'lit_number', 
            'service_department', 'date_entree', 'date_sortie_prevue', 'nombre_jours', 'motif_admission', 
            'diagnostic_entree', 'tarif_journalier', 'frais_soins_chambre', 'part_assurance', 
            'searchPatient', 'selectedPatientName', 'date_sortie_effective', 'diagnostic_sortie', 
            'observations', 'montant_paye', 'montant_verse', 'selectedHospitalisation'
        ]);
        $this->type_prise_en_charge = 'hospitalisation';
        $this->standing_type = 'classique';
        $this->nombre_jours = 1;
    }

    public function delete($id)
    {
        $hosp = Hospitalisation::findOrFail($id);

        // Optionnel : Empêcher la suppression si un paiement ou une avance a déjà été effectué
        if (floatval($hosp->montant_paye) > 0) {
            session()->flash('error', 'Impossible de supprimer cette prise en charge car des paiements ont déjà été enregistrés.');
            return;
        }

        $hosp->delete();

        session()->flash('message', 'La prise en charge a été supprimée avec succès.');
        $this->backToIndex();
    }
}