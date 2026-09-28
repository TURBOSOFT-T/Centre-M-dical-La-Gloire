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
    public $type = 'biologie'; // Valeur par défaut
    public $logo;
    public $caracteristiques = [];

    public function mount($examen = null)
    {
        if ($examen) {
            $this->examen = $examen;
            $this->nom = $examen->nom;
            $this->type = $examen->type ?? 'biologie';

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
            'type'                      => 'required|in:biologie,imagerie,cardiologie,parasitologie,hematologie,biochimie,autre',
            'caracteristiques'          => 'nullable|array',
            'caracteristiques.*.nom'    => 'required_with:caracteristiques.*.prix|nullable|string',
            'caracteristiques.*.prix'   => 'required_with:caracteristiques.*.nom|nullable|numeric|min:0',
        ], [
            'nom.required'                      => 'Le nom du groupe d\'examen est obligatoire.',
            'type.required'                     => 'Le type d\'examen est obligatoire.',
            'type.in'                           => 'Le type d\'examen sélectionné est invalide.',
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
        $examen->type = $this->type;
        $examen->user_id = auth()->id(); // Enregistrement de l'utilisateur connecté
        
        // Attribution du tableau de caractéristiques (géré automatiquement par le $casts en JSON)
        $examen->caracteristiques = $filteredCaracteristiques;
        $examen->save();

        session()->flash('success', $this->examen ? 'Examen mis à jour avec succès' : 'Examen ajouté avec succès');

        $this->reset(['nom', 'type', 'caracteristiques', 'examen']);
        $this->type = 'biologie';
        $this->caracteristiques = [['nom' => '', 'prix' => 0]];

        $this->dispatch('ExamenAdded');
    }

    public function render()
    {
        return view('livewire.examens.add');
    }
}