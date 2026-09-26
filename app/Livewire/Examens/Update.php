<?php

namespace App\Livewire\Examens;

use App\Models\Examen;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public $examen;
    public $nom;
    public $logo;
    public $caracteristiques = [];

    public function mount(Examen $examen = null)
    {
        if ($examen) {
            $this->examen = $examen;
            $this->nom = $examen->nom;

            // Décodage des caractéristiques [ {"nom": "...", "prix": ...} ]
            $this->caracteristiques = is_string($examen->caracteristiques)
                ? json_decode($examen->caracteristiques, true)
                : ($examen->caracteristiques ?? []);
        }

        // Si aucune caractéristique n'existe encore, initialiser avec une ligne vide
        if (empty($this->caracteristiques)) {
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

    public function update()
    {
        $this->validate([
            'nom'                       => 'required|string|max:200',
            'caracteristiques'          => 'nullable|array',
            'caracteristiques.*.nom'    => 'required_with:caracteristiques.*.prix|nullable|string',
            'caracteristiques.*.prix'   => 'required_with:caracteristiques.*.nom|nullable|numeric|min:0',
        ], [
            'nom.required'                          => 'Le nom de l\'examen est obligatoire.',
            'caracteristiques.*.nom.required_with'  => 'Le nom de l\'analyse est requis.',
            'caracteristiques.*.prix.required_with' => 'Le prix de l\'analyse est requis.',
        ]);

        // Filtrer les sous-analyses complètes (nom non vide et prix défini)
        $filteredCaracteristiques = [];
        if (is_array($this->caracteristiques)) {
            $filteredCaracteristiques = array_values(array_filter($this->caracteristiques, function ($item) {
                return !empty($item['nom']) && isset($item['prix']) && $item['prix'] !== '';
            }));
        }

        // Mise à jour du modèle
        $this->examen->nom = $this->nom;
        $this->examen->caracteristiques = $filteredCaracteristiques;
        $this->examen->save();

        session()->flash('success', 'Examen modifié avec succès !');

        return redirect()->route('examens');
    }

    public function render()
    {
        return view('livewire.examens.update');
    }
}