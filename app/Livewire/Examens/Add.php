<?php

namespace App\Livewire\Examens;

use App\Models\Examen;
use Livewire\Component;
use Livewire\WithFileUploads;

class Add extends Component
{
    use WithFileUploads;

    public $examen;
    public $nom;
    public $logo;
    public $caracteristiques = [];

    public function mount($examen = null)
    {
        if ($examen) {
            $this->examen = $examen;
            $this->nom = $examen->nom;

            // Décodage des caractéristiques [ {"nom": "...", "prix": ...} ]
            $this->caracteristiques = is_string($examen->caracteristiques)
                ? json_decode($examen->caracteristiques, true)
                : ($examen->caracteristiques ?? []);
        } else {
            // Ligne par défaut pour l'ajout
            $this->caracteristiques = [
                ['nom' => '', 'prix' => 0]
            ];
        }
    }

    // Ajouter une ligne de sous-analyse
    public function addCaracteristique()
    {
        $this->caracteristiques[] = ['nom' => '', 'prix' => 0];
    }

    // Supprimer une ligne de sous-analyse
    public function removeCaracteristique($index)
    {
        unset($this->caracteristiques[$index]);
        $this->caracteristiques = array_values($this->caracteristiques); // Réindexation du tableau
    }

    public function save()
    {
        $this->validate([
            'nom'                       => 'required|string|max:200',
            'caracteristiques'          => 'nullable|array',
            'caracteristiques.*.nom'    => 'required_with:caracteristiques.*.prix|nullable|string',
            'caracteristiques.*.prix'   => 'required_with:caracteristiques.*.nom|nullable|numeric|min:0',
        ], [
            'nom.required'                          => 'Le nom du groupe d\'examen est obligatoire.',
            'caracteristiques.*.nom.required_with'  => 'Le nom de l\'analyse est requis.',
            'caracteristiques.*.prix.required_with' => 'Le prix de l\'analyse est requis.',
        ]);

        // Filtrer les éléments vides
        $filteredCaracteristiques = [];
        if (is_array($this->caracteristiques)) {
            $filteredCaracteristiques = array_values(array_filter($this->caracteristiques, function ($item) {
                return !empty($item['nom']) && isset($item['prix']) && $item['prix'] !== '';
            }));
        }

        // Création ou Mise à jour
        $examen = $this->examen ?? new Examen();
        $examen->nom = $this->nom;
        
        // Comme le modèle Examen possède $casts = ['caracteristiques' => 'array'], 
        // on assigne directement le tableau (Eloquent gère la sérialisation JSON).
        $examen->caracteristiques = $filteredCaracteristiques;
        $examen->save();

        session()->flash('success', $this->examen ? 'Examen mis à jour avec succès' : 'Examen ajouté avec succès');

        $this->reset(['nom', 'caracteristiques', 'examen']);
        $this->caracteristiques = [['nom' => '', 'prix' => 0]];

        $this->dispatch('ExamenAdded');
    }

    public function render()
    {
        return view('livewire.examens.add');
    }
}