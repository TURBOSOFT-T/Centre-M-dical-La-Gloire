<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Hospitalisation;
use App\Models\User;

class HospitalisationStats extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtres
    public $periode = 'mois'; // 'tous', 'jour', 'semaine', 'mois', 'intervalle'
    public $dateDebut = '';
    public $dateFin = '';
    public $filterStatut = '';
    public $filterAgent = '';
    public $search = '';

    public function mount()
    {
        // Initialiser par défaut l'intervalle avec la date du jour si besoin
        $this->dateDebut = now()->startOfDay()->format('Y-m-d\TH:i');
        $this->dateFin = now()->endOfDay()->format('Y-m-d\TH:i');
    }

    public function updatingPeriode()
    {
        $this->resetPage();
    }

    public function updatingDateDebut()
    {
        $this->resetPage();
    }

    public function updatingDateFin()
    {
        $this->resetPage();
    }

    public function updatingFilterStatut()
    {
        $this->resetPage();
    }

    public function updatingFilterAgent()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Requête de base avec les filtres
        $query = Hospitalisation::with(['patient', 'dossierMedical', 'medecin', 'agent'])
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
            ->when($this->filterAgent, function ($q) {
                $q->where('agent_id', $this->filterAgent);
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

        // Calculs des totaux statistiques sur l'ensemble filtré
        $statsQuery = clone $query;
        $totalAdmissions = $statsQuery->count();
        $montantTotalGlobal = $statsQuery->sum('montant_total');
        $montantEncaisseGlobal = $statsQuery->sum('montant_paye');
        $resteAEncaisser = max(0, $montantTotalGlobal - $montantEncaisseGlobal);

        // Données paginées pour le tableau de détail
        $hospitalisations = $query->latest('created_at')->paginate(10);

        // Récupération sécurisée des agents
        $agentIds = Hospitalisation::whereNotNull('agent_id')->distinct()->pluck('agent_id');
        $agents = User::whereIn('id', $agentIds)->get();

        return view('livewire.hospitalisation-stats', [
            'hospitalisations' => $hospitalisations,
            'agents' => $agents,
            'totalAdmissions' => $totalAdmissions,
            'montantTotalGlobal' => $montantTotalGlobal,
            'montantEncaisseGlobal' => $montantEncaisseGlobal,
            'resteAEncaisser' => $resteAEncaisser,
        ]);
    }
}