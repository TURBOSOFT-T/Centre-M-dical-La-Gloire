<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use App\Models\Examen;
use App\Models\DemandeExamen;
use Livewire\Component;

class PrescrireExamen extends Component
{
    public Consultation $consultation;

    // Champs du formulaire (Création & Édition)
    public $demande_id = null; // Utilisé lors de l'édition d'une prescription
    public $examen_id = '';
    public array $caracteristiquesDisponibles = [];
    public array $analysesSelectionnees = [];
    public string $indication_medicale = '';
    public float $tarifTotal = 0.0;

    // État d'édition
    public bool $isEditing = false;

    protected function rules(): array
    {
        return [
            'examen_id'             => 'required|exists:examens,id',
            'analysesSelectionnees' => 'required|array|min:1',
            'indication_medicale'   => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'examen_id.required'             => 'Veuillez sélectionner un examen dans le catalogue.',
            'analysesSelectionnees.required' => 'Vous devez cocher au moins une analyse/sous-examen.',
            'analysesSelectionnees.min'      => 'Veuillez cocher au moins une analyse.',
        ];
    }

    public function mount(Consultation $consultation)
    {
        $this->consultation = $consultation;
    }

    public function updatedExamenId($value)
    {
        if (!$this->isEditing) {
            $this->analysesSelectionnees = [];
            $this->tarifTotal = 0.0;

            if (!empty($value)) {
                $examen = Examen::find($value);
                if ($examen) {
                    $this->caracteristiquesDisponibles = is_array($examen->caracteristiques)
                        ? $examen->caracteristiques
                        : json_decode($examen->caracteristiques ?? '[]', true);

                    $this->analysesSelectionnees = array_keys($this->caracteristiquesDisponibles);
                    $this->recalculerTarif();
                } else {
                    $this->caracteristiquesDisponibles = [];
                }
            } else {
                $this->caracteristiquesDisponibles = [];
            }
        }
    }

    public function updatedAnalysesSelectionnees()
    {
        $this->recalculerTarif();
    }

    public function recalculerTarif()
    {
        $total = 0.0;
        foreach ($this->analysesSelectionnees as $index) {
            if (isset($this->caracteristiquesDisponibles[$index])) {
                $prix = $this->caracteristiquesDisponibles[$index]['prix'] ?? 0;
                $total += floatval($prix);
            }
        }
        $this->tarifTotal = $total;
    }

    public function toggleToutCocher($cocher = true)
    {
        if ($cocher) {
            $this->analysesSelectionnees = array_keys($this->caracteristiquesDisponibles);
        } else {
            $this->analysesSelectionnees = [];
        }
        $this->recalculerTarif();
    }

    /**
     * Charger un examen prescrit pour modification
     */
    public function editerExamen($demandeId)
    {
        $demande = DemandeExamen::with('examen')->findOrFail($demandeId);

        $this->demande_id = $demande->id;
        $this->examen_id = $demande->examen_id;
        $this->indication_medicale = $demande->indication_medicale ?? '';
        $this->isEditing = true;

        // Récupérer le catalogue complet de cet examen
        $examenCatalogue = $demande->examen;
        $this->caracteristiquesDisponibles = is_array($examenCatalogue->caracteristiques)
            ? $examenCatalogue->caracteristiques
            : json_decode($examenCatalogue->caracteristiques ?? '[]', true);

        // Cocher uniquement les sous-analyses déjà prescrites
        $this->analysesSelectionnees = [];
        $analysesPrescritesNoms = collect($demande->analyses_demandees ?? [])->pluck('nom')->toArray();

        foreach ($this->caracteristiquesDisponibles as $index => $item) {
            if (in_array($item['nom'], $analysesPrescritesNoms)) {
                $this->analysesSelectionnees[] = $index;
            }
        }

        $this->recalculerTarif();
    }

    /**
     * Annuler l'édition en cours
     */
    public function annulerEdition()
    {
        $this->reset(['demande_id', 'examen_id', 'caracteristiquesDisponibles', 'analysesSelectionnees', 'indication_medicale', 'tarifTotal', 'isEditing']);
        $this->resetValidation();
    }

    /**
     * Enregistrer ou mettre à jour la prescription
     */
    public function enregistrer()
    {
        $this->validate();

        $examenCatalogue = Examen::findOrFail($this->examen_id);

        $analysesFinales = [];
        foreach ($this->analysesSelectionnees as $index) {
            if (isset($this->caracteristiquesDisponibles[$index])) {
                $item = $this->caracteristiquesDisponibles[$index];
                $analysesFinales[] = [
                    'nom'      => $item['nom'] ?? 'Analyse',
                    'prix'     => floatval($item['prix'] ?? 0),
                    'resultat' => null,
                    'norme'    => null,
                ];
            }
        }

        $patient = $this->consultation->patient;
        $tauxCouverture = ($patient && $patient->est_assure) ? ($patient->taux_couverture ?? 0) : 0;
        
        $partAssurance = ($this->tarifTotal * $tauxCouverture) / 100;
        $partPatient = $this->tarifTotal - $partAssurance;

        if ($this->isEditing && $this->demande_id) {
            // Mettre à jour la demande existante
            $demande = DemandeExamen::findOrFail($this->demande_id);
            $demande->update([
                'indication_medicale' => $this->indication_medicale,
                'analyses_demandees'  => $analysesFinales,
                'tarif_brut'          => $this->tarifTotal,
                'part_assurance'      => $partAssurance,
                'part_patient'        => $partPatient,
            ]);

            session()->flash('success_examen', 'La prescription d\'examen a été modifiée avec succès.');
        } else {
            // Créer une nouvelle demande
            DemandeExamen::create([
                'consultation_id'     => $this->consultation->id,
                'examen_id'           => $this->examen_id,
                'patient_id'          => $this->consultation->patient_id,
                'dossier_medical_id'  => $this->consultation->dossier_medical_id,
                'prescrit_par'        => auth()->id() ?? $this->consultation->medecin_id,
                'indication_medicale' => $this->indication_medicale,
                'analyses_demandees'  => $analysesFinales,
                'statut'              => 'prescrit',
                'tarif_brut'          => $this->tarifTotal,
                'part_assurance'      => $partAssurance,
                'part_patient'        => $partPatient,
                'est_paye'            => false,
            ]);

            session()->flash('success_examen', 'L\'examen (' . $examenCatalogue->nom . ') a été prescrit avec succès.');
        }

        $this->annulerEdition();
        $this->dispatch('examenPrescrit');
    }

    /**
     * Supprimer un examen prescrit
     */
    public function supprimerExamen($demandeId)
    {
        $demande = DemandeExamen::findOrFail($demandeId);
        
        // Empêcher la suppression si l'examen a déjà été payé ou réalisé
        if ($demande->est_paye || in_array($demande->statut, ['en_cours', 'termine'])) {
            session()->flash('error_examen', 'Impossible de supprimer un examen déjà réglé à la caisse ou en cours de réalisation.');
            return;
        }

        $demande->delete();

        session()->flash('success_examen', 'L\'examen prescrit a été retiré de la consultation.');
        
        if ($this->demande_id == $demandeId) {
            $this->annulerEdition();
        }

        $this->dispatch('examenPrescrit');
    }

    public function render()
    {
        return view('livewire.consultations.prescrire-examen', [
            'examensCatalogue' => Examen::orderBy('nom', 'asc')->get(),
            'examensPrescrits' => $this->consultation->demandesExamens()->with(['examen', 'prescripteur'])->latest()->get(),
        ]);
    }
}