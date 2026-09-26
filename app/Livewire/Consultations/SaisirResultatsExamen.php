<?php

namespace App\Livewire\Consultations;

use App\Models\DemandeExamen;
use Livewire\Component;

class SaisirResultatsExamen extends Component
{
    public DemandeExamen $demandeExamen;

    // Tableau dynamique structuré : [index => ['nom' => ..., 'prix' => ..., 'resultat' => ..., 'norme' => ...]]
    public array $analysesResultats = [];
    public string $conclusion = '';
    public string $statut = 'termine';

    public function mount(DemandeExamen $demandeExamen)
    {
        $this->demandeExamen = $demandeExamen;
        $this->analysesResultats = is_array($demandeExamen->analyses_demandees)
            ? $demandeExamen->analyses_demandees
            : json_decode($demandeExamen->analyses_demandees ?? '[]', true);

        $this->conclusion = $demandeExamen->conclusion ?? '';
        $this->statut = $demandeExamen->statut ?? 'termine';
    }

    public function enregistrerResultats()
    {
        $this->validate([
            'analysesResultats.*.resultat' => 'nullable|string|max:255',
            'analysesResultats.*.norme'    => 'nullable|string|max:255',
            'conclusion'                   => 'nullable|string|max:1000',
            'statut'                       => 'required|in:prescrit,en_cours,termine',
        ]);

        $this->demandeExamen->update([
            'analyses_demandees' => $this->analysesResultats,
            'conclusion'         => $this->conclusion,
            'statut'             => $this->statut,
           'effectue_par'        => auth()->id(),
            'date_realisation'   => now(),
        ]);

        session()->flash('success_resultats_' . $this->demandeExamen->id, 'Résultats enregistrés avec succès.');

        // Émettre un événement pour rafraîchir la modale de consultation
        $this->dispatch('examenPrescrit');
    }

    public function render()
    {
        return view('livewire.consultations.saisir-resultats-examen');
    }
}