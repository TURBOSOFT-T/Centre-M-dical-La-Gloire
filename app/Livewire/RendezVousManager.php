<?php

namespace App\Livewire;

use App\Http\Traits\TypeConsultations;
use App\Models\config;
use App\Models\Configuration;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class RendezVousManager extends Component
{
    use WithPagination;
    use TypeConsultations;

    // Mode d'affichage : 'index', 'create', 'edit', 'show'
    public string $mode = 'index';

    // Recherche et filtres
    public string $search = '';
    public string $filterStatut = '';

    // Instance sélectionnée
    public ?RendezVous $selectedRdv = null;

    // Champs du formulaire
    public $rdv_id;
    public $patient_id;
    public $medecin_id;
    public $date_heure;
    public string $type = 'consultation_generale';
    public $tarif_brut = 0;
    public $part_assurance = 0;
    public $part_patient = 0;
    public $montant_paye = 0;
    public string $statut_paiement = 'non_paye';
    public $mode_paiement;
    public string $statut = 'planifie';
    public $motif;

    // --- Durées estimées par type de rendez-vous (en minutes) ---
    protected array $durations = [
        'consultation_generale' => 30,
        'consultation_specialisee' => 45,
        'suivi' => 20,
        'urgence' => 30,
    ];

    // --- Variables d'autocomplétion ---
    public string $searchPatient = '';
    public string $selectedPatientName = '';
    public bool $showPatientDropdown = false;

    public string $searchMedecin = '';
    public string $selectedMedecinName = '';
    public bool $showMedecinDropdown = false;

    protected function rules(): array
    {
        return [
            'patient_id'      => 'required|exists:patients,id',
            'medecin_id'      => 'nullable|exists:users,id',
            'date_heure'      => 'required|date',
            'type'            => 'required|string',
            'tarif_brut'      => 'required|numeric|min:0',
            'part_assurance'  => 'nullable|numeric|min:0',
            'part_patient'    => 'required|numeric|min:0',
            'montant_paye'    => 'nullable|numeric|min:0',
            'statut_paiement' => 'required|in:non_paye,partiel,paye,rembourse',
            'mode_paiement'   => 'nullable|in:especes,mobile_money,carte_bancaire,assurance,autre',
            'statut'          => 'required|in:planifie,confirme,en_attente,honore,annule,absent',
            'motif'           => 'nullable|string',
        ];
    }

    // --- Téléchargement PDF de la facture ---
    public function downloadFacture($id)
    {
        $rdv = RendezVous::with(['patient.assurance', 'medecin'])->findOrFail($id);
        $config = config::first();

        // Encodage du logo en base64 pour DomPDF
        $logoBase64 = null;
        if ($config && $config->logo && file_exists(public_path('storage/' . $config->logo))) {
            $path = public_path('storage/' . $config->logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('pdf.facture', [
            'selectedRdv' => $rdv,
            'config'      => $config,
            'logoBase64'  => $logoBase64,
        ])->setPaper('a4', 'portrait');

        $fileName = 'facture-' . ($rdv->code_rdv ?? $rdv->id) . '.pdf';

        // Correction ici : utilisation d'une fonction anonyme classique
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName);
    }

    // --- Écouteurs de mise à jour des prix ---
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatut(): void
    {
        $this->resetPage();
    }

    public function updatedTarifBrut(): void
    {
        $this->calculerReste();
    }

    public function updatedPartAssurance(): void
    {
        $this->calculerReste();
    }

    private function calculerReste(): void
    {
        $tarif = (float) $this->tarif_brut;
        $assurance = (float) $this->part_assurance;
        $this->part_patient = max(0, $tarif - $assurance);
    }

    // --- Validation de disponibilité du médecin ---
    private function validateDoctorAvailability(): bool
    {
        if (!$this->medecin_id || !$this->date_heure) {
            return true;
        }

        $start = Carbon::parse($this->date_heure);
        $durationMinutes = $this->durations[$this->type] ?? 30;
        $end = (clone $start)->addMinutes($durationMinutes);

        $hasConflict = RendezVous::where('medecin_id', $this->medecin_id)
            ->whereNotIn('statut', ['annule', 'absent'])
            ->when($this->mode === 'edit' && $this->rdv_id, function ($query) {
                $query->where('id', '!=', $this->rdv_id);
            })
            ->where(function ($query) use ($start, $end, $durationMinutes) {
                $query->where('date_heure', '<', $end->format('Y-m-d H:i:s'))
                    ->whereRaw("DATE_ADD(date_heure, INTERVAL ? MINUTE) > ?", [$durationMinutes, $start->format('Y-m-d H:i:s')]);
            })
            ->exists();

        if ($hasConflict) {
            $this->addError('date_heure', 'Le médecin sélectionné a déjà un rendez-vous prévu sur ce créneau.');
            return false;
        }

        return true;
    }

    // --- Méthodes de Sélection Patient ---
    public function selectPatient($id, string $nomComplet): void
    {
        $this->patient_id = $id;
        $this->selectedPatientName = $nomComplet;
        $this->searchPatient = $nomComplet;
        $this->showPatientDropdown = false;
    }

    public function clearPatient(): void
    {
        $this->patient_id = null;
        $this->selectedPatientName = '';
        $this->searchPatient = '';
        $this->showPatientDropdown = true;
    }

    // --- Méthodes de Sélection Médecin ---
    public function selectMedecin($id, string $nomComplet): void
    {
        $this->medecin_id = $id;
        $this->selectedMedecinName = $nomComplet;
        $this->searchMedecin = $nomComplet;
        $this->showMedecinDropdown = false;
    }

    public function clearMedecin(): void
    {
        $this->medecin_id = null;
        $this->selectedMedecinName = '';
        $this->searchMedecin = '';
        $this->showMedecinDropdown = true;
    }

    public function resetFields(): void
    {
        $this->reset([
            'rdv_id',
            'patient_id',
            'medecin_id',
            'date_heure',
            'type',
            'tarif_brut',
            'part_assurance',
            'part_patient',
            'montant_paye',
            'statut_paiement',
            'mode_paiement',
            'statut',
            'motif',
            'selectedRdv',
            'searchPatient',
            'selectedPatientName',
            'showPatientDropdown',
            'searchMedecin',
            'selectedMedecinName',
            'showMedecinDropdown'
        ]);
        $this->resetValidation();
    }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->mode = 'create';
    }

    public function openShow($id): void
    {
        $this->selectedRdv = RendezVous::with(['patient.assurance', 'medecin', 'agent'])->findOrFail($id);
        $this->mode = 'show';
    }

    public function openEdit($id): void
    {
        $this->resetFields();
        $rdv = RendezVous::with(['patient', 'medecin'])->findOrFail($id);

        $this->rdv_id          = $rdv->id;
        $this->patient_id      = $rdv->patient_id;
        $this->medecin_id      = $rdv->medecin_id;
        $this->date_heure      = $rdv->date_heure?->format('Y-m-d\TH:i');
        $this->type            = $rdv->type ?? 'consultation_generale';
        $this->tarif_brut      = $rdv->tarif_brut;
        $this->part_assurance  = $rdv->part_assurance;
        $this->part_patient    = $rdv->part_patient;
        $this->montant_paye    = $rdv->montant_paye;
        $this->statut_paiement = $rdv->statut_paiement ?? 'non_paye';
        $this->mode_paiement   = $rdv->mode_paiement;
        $this->statut          = $rdv->statut ?? 'planifie';
        $this->motif           = $rdv->motif;

        if ($rdv->patient) {
            $this->selectedPatientName = trim(($rdv->patient->nom ?? '') . ' ' . ($rdv->patient->prenom ?? ''));
            $this->searchPatient = $this->selectedPatientName;
        }

        if ($rdv->medecin) {
            $this->selectedMedecinName = 'Dr. ' . trim(($rdv->medecin->name ?? ($rdv->medecin->nom ?? '')) . ' ' . ($rdv->medecin->prenom ?? ''));
            $this->searchMedecin = $this->selectedMedecinName;
        }

        $this->mode = 'edit';
    }

    public function backToIndex(): void
    {
        $this->resetFields();
        $this->mode = 'index';
    }

    public function save(): void
    {
        $this->calculerReste();
        $validated = $this->validate();

        if (!$this->validateDoctorAvailability()) {
            return;
        }

        if ($this->mode === 'create') {
            $validated['code_rdv'] = 'RDV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $validated['cree_par'] = auth()->id();
            RendezVous::create($validated);
            session()->flash('message', 'Rendez-vous créé avec succès.');
        } elseif ($this->mode === 'edit') {
            $rdv = RendezVous::findOrFail($this->rdv_id);
            $rdv->update($validated);
            session()->flash('message', 'Rendez-vous mis à jour avec succès.');
        }

        $this->backToIndex();
    }

    public function delete($id): void
    {
        RendezVous::findOrFail($id)->delete();
        session()->flash('message', 'Rendez-vous supprimé.');

        if ($this->mode === 'show') {
            $this->backToIndex();
        }
    }

    public function render()
    {
        $patientsFound = collect();
        if (strlen(trim($this->searchPatient)) >= 2 && !$this->patient_id) {
            $searchTerm = '%' . trim($this->searchPatient) . '%';
            $patientsFound = Patient::where('nom', 'like', $searchTerm)
                ->orWhere('prenom', 'like', $searchTerm)
                ->orWhere('telephone', 'like', $searchTerm)
                ->take(5)
                ->get();
        }

        $medecinsFound = collect();
        if (strlen(trim($this->searchMedecin)) >= 2 && !$this->medecin_id) {
            $cleanSearch = '%' . trim(str_replace(['Dr.', 'Dr '], '', $this->searchMedecin)) . '%';
            $medecinsFound = User::where(function ($q) {
                $q->where('role', 'medecin')
                    ->orWhere('role', 'like', '%medecin%');
            })
                ->where(function ($query) use ($cleanSearch) {
                    $query->where('email', 'like', $cleanSearch)
                        ->orWhere('nom', 'like', $cleanSearch)
                        ->orWhere('prenom', 'like', $cleanSearch);
                })
                ->take(5)
                ->get();
        }

        $rendezVousList = RendezVous::with(['patient', 'medecin'])
            ->when($this->search, function ($query) {
                $query->where('code_rdv', 'like', '%' . $this->search . '%')
                    ->orWhereHas('patient', function ($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%')
                            ->orWhere('prenom', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterStatut, fn($q) => $q->where('statut', $this->filterStatut))
            ->latest()
            ->paginate(10);

        return view('livewire.rendez-vous-manager', [
            'rendezVousList' => $rendezVousList,
            'patientsFound'  => $patientsFound,
            'medecinsFound'  => $medecinsFound,
        ]);
    }
}
