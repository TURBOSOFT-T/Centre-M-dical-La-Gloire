<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Consultation;
use App\Models\User;

class ConsultationStats extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $periode = 'mois';
    public $dateDebut = '';
    public $dateFin = '';
    public $filterStatut = '';
    public $filterStatutPaiement = ''; // <--- AJOUTÉ ICI
    public $filterMedecin = '';
    public $search = '';

    public function mount()
    {
        $this->dateDebut = now()->startOfDay()->format('Y-m-d\TH:i');
        $this->dateFin = now()->endOfDay()->format('Y-m-d\TH:i');
    }

    public function updatingPeriode() { $this->resetPage(); }
    public function updatingDateDebut() { $this->resetPage(); }
    public function updatingDateFin() { $this->resetPage(); }
    public function updatingFilterStatut() { $this->resetPage(); }
    public function updatingFilterStatutPaiement() { $this->resetPage(); } // <--- AJOUTÉ ICI
    public function updatingFilterMedecin() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $query = Consultation::with(['patient', 'medecin', 'demandesExamens'])
            ->when($this->search, function ($q) {
                $q->where('code_consultation', 'like', '%' . $this->search . '%')
                  ->orWhereHas('patient', function ($qp) {
                      $qp->where('nom', 'like', '%' . $this->search . '%')
                         ->orWhere('prenom', 'like', '%' . $this->search . '%')
                         ->orWhere('telephone', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->filterStatut, function ($q) {
                $q->where('statut', $this->filterStatut);
            })
            ->when($this->filterStatutPaiement, function ($q) { // <--- FILTRE DE PAIEMENT APPLIQUÉ
                $q->where('statut_paiement', $this->filterStatutPaiement);
            })
            ->when($this->filterMedecin, function ($q) {
                $q->where('medecin_id', $this->filterMedecin);
            })
            ->when($this->periode === 'jour', function ($q) {
                $q->whereDate('created_at', today());
            })
            ->when($this->periode === 'semaine', function ($q) {
                $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            })
            ->when($this->periode === 'mois', function ($q) {
                $q->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            })
            ->when($this->periode === 'intervalle', function ($q) {
                if ($this->dateDebut) {
                    $q->where('created_at', '>=', $this->dateDebut);
                }
                if ($this->dateFin) {
                    $q->where('created_at', '<=', $this->dateFin);
                }
            });

        $statsQuery = clone $query;
        $allConsultations = $statsQuery->get();

        $totalConsultations = $allConsultations->count();
        $montantTotalGlobal = $allConsultations->sum(fn($c) => $c->total_facture);
        $montantEncaisseGlobal = $allConsultations->sum('montant_paye');
        $resteAEncaisser = max(0, $montantTotalGlobal - $montantEncaisseGlobal);

        $consultations = $query->latest('created_at')->paginate(10);

        $medecinIds = Consultation::whereNotNull('medecin_id')->distinct()->pluck('medecin_id');
        $medecins = User::whereIn('id', $medecinIds)->get();

        return view('livewire.consultation-stats', [
            'consultations' => $consultations,
            'medecins' => $medecins,
            'agents' => $medecins,
            'totalConsultations' => $totalConsultations,
            'montantTotalGlobal' => $montantTotalGlobal,
            'montantEncaisseGlobal' => $montantEncaisseGlobal,
            'resteAEncaisser' => $resteAEncaisser,
        ]);
    }
}