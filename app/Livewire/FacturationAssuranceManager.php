<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Consultation;
use App\Models\Patient;

class FacturationAssuranceManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filtreAssurance = '';
    public $filtreDate = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Générer / Imprimer la facture spécifique des médicaments
     */
    public function genererFactureMedicaments($consultationId)
    {
        return redirect()->route('factures.medicaments', ['id' => $consultationId]);
    }

    /**
     * Générer / Imprimer la facture spécifique des examens
     */
    public function genererFactureExamens($consultationId)
    {
        return redirect()->route('factures.examens', ['id' => $consultationId]);
    }

    /**
     * Générer / Imprimer la facture récapitulative globale (Consultation + Hosp. + Examens + Médicaments)
     */
    public function genererFactureGlobale($consultationId)
    {
        return redirect()->route('factures.globale', ['id' => $consultationId]);
    }

    public function render()
    {
        $userId = auth()->id();
        $user = auth()->user();

        // Récupération uniquement des consultations de patients ASSURÉS
        $consultations = Consultation::query()
            ->whereHas('patient', function ($q) {
                $q->where('est_assure', true)
                  ->orWhereNotNull('assurance_id');
            })
            ->with(['patient.assurance', 'medecin', 'demandesExamens', 'produits'])
            // Respect de la visibilité selon les rôles
            ->when($user && !$user->hasRole('admin') && !$user->is_admin, function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('medecin_id', $userId)
                      ->orWhere('user_id', $userId);
                });
            })
            // Recherche par nom, prénom, matricule ou code
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('code_patient', 'like', '%' . $this->search . '%');
                   
                })->orWhere('code_consultation', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtreDate, function ($query) {
                $query->whereDate('date_heure_rdv', $this->filtreDate);
            })
            ->latest('date_heure_rdv')
            ->paginate(10);

        return view('livewire.facturation-assurance-manager', [
            'consultations' => $consultations,
        ]);
    }
}