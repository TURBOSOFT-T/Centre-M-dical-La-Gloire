<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DossierMedical;
use App\Models\Patient;
use App\Models\DemandeExamen;

class DossierMedicalManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $mode = 'index'; // 'index', 'show', 'create', 'edit'
    public $search = '';
    public $activeTab = 'apercu'; // 'apercu', 'consultations', 'hospitalisations', 'rendezvous', 'factures', 'examens'

    public $selectedDossier = null;

    // Gestion de la modale de visualisation des résultats d'examens
    public $isModalResultatsOpen = false;
    public $selectedDemandeExamen = null;

    // Champs Formulaire
    public $dossierId;
    public $patient_id;
    public $searchPatient = '';
    public $selectedPatientName = '';
    public $groupe_sanguin = '';
    public $antecedents_medicaux = '';
    public $antecedents_chirurgicaux = '';
    public $allergies = '';
    public $traitements_chroniques = '';
    public $statut = 'actif';

    // Réinitialise la pagination lors d'une recherche
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $dossiers = DossierMedical::with('patient')
            ->when($this->search, function ($q) {
                $q->where('code_dossier', 'like', '%' . $this->search . '%')
                  ->orWhereHas('patient', function ($qp) {
                      $qp->where('nom', 'like', '%' . $this->search . '%')
                         ->orWhere('prenom', 'like', '%' . $this->search . '%')
                         ->orWhere('telephone', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(10);

        $patientsFound = [];
        if (!empty($this->searchPatient) && !$this->selectedPatientName) {
            $patientsFound = Patient::whereDoesntHave('dossierMedical')
                ->where(function ($q) {
                    $q->where('nom', 'like', '%' . $this->searchPatient . '%')
                      ->orWhere('prenom', 'like', '%' . $this->searchPatient . '%')
                      ->orWhere('telephone', 'like', '%' . $this->searchPatient . '%');
                })
                ->take(5)
                ->get();
        }

        return view('livewire.dossier-medical-manager', [
            'dossiers' => $dossiers,
            'patientsFound' => $patientsFound,
        ]);
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->mode = 'create';
    }

    public function store()
    {
        $this->validate([
            'patient_id' => 'required|unique:dossiers_medicaux,patient_id',
            'groupe_sanguin' => 'nullable|string|max:10',
            'antecedents_medicaux' => 'nullable|string',
            'antecedents_chirurgicaux' => 'nullable|string',
            'allergies' => 'nullable|string',
            'traitements_chroniques' => 'nullable|string',
            'statut' => 'required|in:actif,archive,decede',
        ], [
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'patient_id.unique' => 'Ce patient possède déjà un dossier médical.',
        ]);

        $codeDossier = 'DM-' . date('Y') . '-' . str_pad(DossierMedical::count() + 1, 4, '0', STR_PAD_LEFT);

        DossierMedical::create([
            'code_dossier' => $codeDossier,
            'patient_id' => $this->patient_id,
            'groupe_sanguin' => $this->groupe_sanguin,
            'antecedents_medicaux' => $this->antecedents_medicaux,
            'antecedents_chirurgicaux' => $this->antecedents_chirurgicaux,
            'allergies' => $this->allergies,
            'traitements_chroniques' => $this->traitements_chroniques,
            'statut' => $this->statut,
        ]);

        session()->flash('message', 'Dossier médical créé avec succès (Code : ' . $codeDossier . ').');
        $this->backToIndex();
    }
public function openShow($id)
{
    $this->selectedDossier = DossierMedical::with([
        'patient.assurance',
        'consultations.demandesExamens.examen', // Charge correctement les examens via les consultations
        'hospitalisations',
        'rendezVous',
    ])->findOrFail($id);

    $this->activeTab = 'apercu';
    $this->mode = 'show';
}

    public function openEdit($id)
    {
        $dossier = DossierMedical::with('patient')->findOrFail($id);

        $this->dossierId = $dossier->id;
        $this->patient_id = $dossier->patient_id;
        $this->selectedPatientName = $dossier->patient ? $dossier->patient->nom . ' ' . $dossier->patient->prenom : '';
        $this->groupe_sanguin = $dossier->groupe_sanguin;
        $this->antecedents_medicaux = $dossier->antecedents_medicaux;
        $this->antecedents_chirurgicaux = $dossier->antecedents_chirurgicaux;
        $this->allergies = $dossier->allergies;
        $this->traitements_chroniques = $dossier->traitements_chroniques;
        $this->statut = $dossier->statut;

        $this->mode = 'edit';
    }

    public function update()
    {
        $this->validate([
            'patient_id' => 'required|unique:dossiers_medicaux,patient_id,' . $this->dossierId,
            'groupe_sanguin' => 'nullable|string|max:10',
            'antecedents_medicaux' => 'nullable|string',
            'antecedents_chirurgicaux' => 'nullable|string',
            'allergies' => 'nullable|string',
            'traitements_chroniques' => 'nullable|string',
            'statut' => 'required|in:actif,archive,decede',
        ], [
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'patient_id.unique' => 'Ce patient est déjà lié à un autre dossier médical.',
        ]);

        $dossier = DossierMedical::findOrFail($this->dossierId);

        $dossier->update([
            'patient_id' => $this->patient_id,
            'groupe_sanguin' => $this->groupe_sanguin,
            'antecedents_medicaux' => $this->antecedents_medicaux,
            'antecedents_chirurgicaux' => $this->antecedents_chirurgicaux,
            'allergies' => $this->allergies,
            'traitements_chroniques' => $this->traitements_chroniques,
            'statut' => $this->statut,
        ]);

        session()->flash('message', 'Dossier médical mis à jour avec succès.');
        $this->backToIndex();
    }

    public function delete($id)
    {
        $dossier = DossierMedical::findOrFail($id);
        $dossier->delete();

        session()->flash('message', 'Dossier médical supprimé avec succès.');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Ouvre la modale pour voir les résultats d'un examen spécifique
     */
    public function voirResultatsExamen($demandeId)
    {
        $this->selectedDemandeExamen = DemandeExamen::with(['examen', 'prescripteur'])->findOrFail($demandeId);
        $this->isModalResultatsOpen = true;
    }

    public function fermerModalResultats()
    {
        $this->isModalResultatsOpen = false;
        $this->selectedDemandeExamen = null;
    }

    public function selectPatient($id, $name)
    {
        $this->patient_id = $id;
        $this->selectedPatientName = $name;
        $this->searchPatient = '';
    }

    public function clearPatient()
    {
        $this->patient_id = null;
        $this->selectedPatientName = '';
        $this->searchPatient = '';
    }

    public function backToIndex()
    {
        $this->resetForm();
        $this->mode = 'index';
    }

    private function resetForm()
    {
        $this->reset([
            'selectedDossier', 'dossierId', 'patient_id', 'searchPatient',
            'selectedPatientName', 'groupe_sanguin', 'antecedents_medicaux',
            'antecedents_chirurgicaux', 'allergies', 'traitements_chroniques',
            'selectedDemandeExamen', 'isModalResultatsOpen'
        ]);
        $this->statut = 'actif';
    }
}