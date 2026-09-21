<?php

namespace App\Livewire\Visites;

use App\Models\Patient;
use App\Models\Visite;
use App\Models\Visiteur;
use Livewire\Component;
use Livewire\WithPagination;

class GestionVisites extends Component
{
    use WithPagination;

    // Champs du formulaire Visite
    public $patient_id;
    public $searchPatient = ''; // Recherche dynamique du patient dans le modal
    public $nom_complet;
    public $telephone;
    public $cni_ou_piece;
    public $lien_parente;
    public $chambre_lit;
    public $badge_numero;
    public $observations;

    // Filtres & Modals
    public $search = '';
    public $isModalOpen = false;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'cni_ou_piece' => 'nullable|string|max:100',
            'lien_parente' => 'nullable|string|max:100',
            'chambre_lit' => 'nullable|string|max:100',
            'badge_numero' => 'nullable|integer',
            'observations' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Autocomplétion : Déclenché automatiquement dès la saisie du numéro de téléphone
     */
    public function updatedTelephone($value)
    {
        $telClean = trim($value);

        if (strlen($telClean) >= 8) {
            $visiteur = Visiteur::where('telephone', $telClean)->first();

            if ($visiteur) {
                $this->nom_complet = $visiteur->nom_complet;
                $this->cni_ou_piece = $visiteur->cni_ou_piece;
                $this->lien_parente = $visiteur->lien_parente;

                session()->flash('info_visiteur', 'Visiteur existant identifié : ' . $visiteur->nom_complet);
            }
        }
    }

    /**
     * Sélection d'un patient à partir de la liste de recherche
     */
    public function selectPatient($id)
    {
        $this->patient_id = $id;
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->patient_id = '';
        $this->searchPatient = '';
        $this->nom_complet = '';
        $this->telephone = '';
        $this->cni_ou_piece = '';
        $this->lien_parente = '';
        $this->chambre_lit = '';
        $this->badge_numero = '';
        $this->observations = '';
        $this->resetValidation();
    }

    /**
     * Enregistre le visiteur et crée le billet de visite
     */
    public function enregistrerVisite()
    {
        $this->validate();

        // 1. Recherche ou création du visiteur
        $visiteur = Visiteur::firstOrCreate(
            ['telephone' => trim($this->telephone)],
            [
                'nom_complet' => $this->nom_complet,
                'cni_ou_piece' => $this->cni_ou_piece ?: null,
                'lien_parente' => $this->lien_parente ?: null,
            ]
        );

        // Mettre à jour les informations du visiteur s'il a changé de nom ou de CNI
        $visiteur->update([
            'nom_complet' => $this->nom_complet,
            'cni_ou_piece' => $this->cni_ou_piece ?: $visiteur->cni_ou_piece,
            'lien_parente' => $this->lien_parente ?: $visiteur->lien_parente,
        ]);

        // 2. Création de la visite
        $visite = Visite::create([
            'code_visite' => 'VIS-' . date('Y') . '-' . strtoupper(uniqid()),
            'patient_id' => $this->patient_id,
            'visiteur_id' => $visiteur->id,
            'user_id' => auth()->id(),
            'date_heure_entree' => now(),
            'chambre_lit' => $this->chambre_lit ?: null,
            'badge_numero' => is_numeric($this->badge_numero) ? (int)$this->badge_numero : null,
            'observations' => $this->observations ?: null,
        ]);

        session()->flash('message', 'Visite enregistrée avec succès.');
        $this->closeModal();

        // Émission d'évènement JavaScript pour l'impression du Pass
        $this->dispatch('imprimerPassVisite', ['url' => route('visites.pass.pdf', $visite->id)]);
    }

    /**
     * Enregistre l'heure de départ/sortie du visiteur
     */
    public function marquerSortie($visiteId)
    {
        $visite = Visite::findOrFail($visiteId);
        $visite->update([
            'date_heure_sortie' => now(),
        ]);

        session()->flash('message', 'Sortie du visiteur enregistrée.');
    }

    public function render()
    {
        // Recherche globale dans le registre des visites
        $visites = Visite::with(['patient', 'visiteur', 'agent'])
            ->where(function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('code_patient', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('visiteur', function ($q) {
                    $q->where('nom_complet', 'like', '%' . $this->search . '%')
                      ->orWhere('telephone', 'like', '%' . $this->search . '%');
                })
                ->orWhere('code_visite', 'like', '%' . $this->search . '%');
            })
            ->orderBy('date_heure_entree', 'desc')
            ->paginate(15);

        // Recherche dynamique des patients pour la modale (limite à 10)
        $patients = Patient::query()
            ->when($this->searchPatient, function ($q) {
                $q->where('nom', 'like', '%' . $this->searchPatient . '%')
                  ->orWhere('prenom', 'like', '%' . $this->searchPatient . '%')
                  ->orWhere('telephone', 'like', '%' . $this->searchPatient . '%')
                  ->orWhere('code_patient', 'like', '%' . $this->searchPatient . '%');
            })
            ->orderBy('nom', 'asc')
            ->take(10)
            ->get();

        return view('livewire.visites.gestion-visites', [
            'visites' => $visites,
            'patients' => $patients,
        ]);
    }
}