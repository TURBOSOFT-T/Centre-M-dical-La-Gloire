<?php

namespace App\Livewire\Assurances;

use App\Models\Assurance;
use Livewire\Component;
use Livewire\WithPagination;

class GestionAssurances extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Recherche et Filtres
    public $search = '';
    public $filtreStatut = '';

    // Propriétés du Formulaire
    public $assurance_id;
    public $code, $nom, $telephone, $email, $adresse;
    public $taux_couverture_defaut = 80;
    public $est_actif = true;

    // Gestion Modale
    public $isModalOpen = false;
    public $isEditMode = false;

    protected function rules()
    {
        return [
            'code' => 'required|string|max:20|unique:assurances,code,' . $this->assurance_id,
            'nom' => 'required|string|max:150',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'adresse' => 'nullable|string|max:150',
            'taux_couverture_defaut' => 'required|integer|min:0|max:100',
            'est_actif' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->isEditMode = false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->assurance_id = null;
        $this->code = '';
        $this->nom = '';
        $this->telephone = '';
        $this->email = '';
        $this->adresse = '';
        $this->taux_couverture_defaut = 80;
        $this->est_actif = true;
        $this->resetValidation();
    }

    public function saveAssurance()
    {
        $validatedData = $this->validate();

        Assurance::updateOrCreate(['id' => $this->assurance_id], $validatedData);

        session()->flash('message', $this->isEditMode 
            ? 'Compagnie d\'assurance mise à jour avec succès.' 
            : 'Nouvelle compagnie d\'assurance ajoutée avec succès.');

        $this->closeModal();
    }

    public function editAssurance($id)
    {
        $assurance = Assurance::findOrFail($id);
        $this->assurance_id = $assurance->id;
        $this->code = $assurance->code;
        $this->nom = $assurance->nom;
        $this->telephone = $assurance->telephone;
        $this->email = $assurance->email;
        $this->adresse = $assurance->adresse;
        $this->taux_couverture_defaut = $assurance->taux_couverture_defaut;
        $this->est_actif = (bool) $assurance->est_actif;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function toggleStatut($id)
    {
        $assurance = Assurance::findOrFail($id);
        $assurance->est_actif = !$assurance->est_actif;
        $assurance->save();

        session()->flash('message', 'Statut de la compagnie modifié.');
    }

    public function deleteAssurance($id)
    {
        $assurance = Assurance::findOrFail($id);
        
        // Vérification si des patients y sont rattachés
        if ($assurance->patients()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer cette assurance car des patients y sont rattachés.');
            return;
        }

        $assurance->delete();
        session()->flash('message', 'Compagnie d\'assurance supprimée avec succès.');
    }

    public function render()
    {
        $assurances = Assurance::query()
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtreStatut !== '', function ($query) {
                $query->where('est_actif', $this->filtreStatut);
            })
            ->withCount('patients')
            ->orderBy('nom', 'asc')
            ->paginate(10);

        return view('livewire.assurances.gestion-assurances', compact('assurances'));
    }
}