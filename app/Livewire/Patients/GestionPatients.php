<?php

namespace App\Livewire\Patients;

use App\Models\Assurance;
use App\Models\Patient;
use App\Models\Visite;
use App\Models\Visiteur;
use Livewire\Component;
use Livewire\WithPagination;

class GestionPatients extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtres
    public $search = '';
    public $filtreGenre = '';

    // Champs de formulaire Patient
    public $patient_id;
    public $nom, $prenom, $genre = 'M', $date_naissance, $lieu_naissance;
    public $lieu_residence, $profession, $religion;
    public $telephone, $telephone_whatsapp, $email, $adresse, $ville = 'Douala';

    // Assurances Santé (Dynamiques)
    public $est_assure = false;
    public $assurance_id;
    public $nom_assure;
    public $matricule_assurance;
    public $taux_couverture = 0;

    // Constantes Médicales & Paramètres Vitaux
    public $poids, $taille, $temperature, $tension, $pouls, $spo2, $imc;

    // Antécédents
    public $groupe_sanguin, $allergies, $antecedents_medicaux;

    // Formulaire d'Ajout Rapide de Visiteur (dans le dossier patient)
    public $visiteur_nom;
    public $visiteur_telephone;
    public $visiteur_lien;
    public $visiteur_cni;
    public $chambre_lit;
    public $badge_numero;
    public $observations;

    // Modales
    public $isModalOpen = false;
    public $isEditMode = false;
    public $selectedPatient = null;
    public $isViewModalOpen = false;

    protected function rules()
    {
        return [
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'genre' => 'required|in:M,F',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:100',
            'lieu_residence' => 'nullable|string|max:150',
            'profession' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',

            'est_assure' => 'boolean',
            'assurance_id' => 'nullable|required_if:est_assure,true|exists:assurances,id',
            'nom_assure' => 'nullable|string|max:150',
            'matricule_assurance' => 'nullable|string|max:100',
            'taux_couverture' => 'nullable|integer|min:0|max:100',

            'telephone' => 'required|string|max:20|unique:patients,telephone,' . $this->patient_id,
            'telephone_whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'adresse' => 'nullable|string|max:150',
            'ville' => 'nullable|string|max:100',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'antecedents_medicaux' => 'nullable|string',
        ];
    }

    public function updatedEstAssure($value)
    {
        if (!$value) {
            $this->assurance_id = null;
            $this->nom_assure = null;
            $this->matricule_assurance = null;
            $this->taux_couverture = 0;
        }
    }

    /**
     * Pré-remplit le taux de couverture par défaut lorsqu'une assurance est choisie
     */
    public function updatedAssuranceId($value)
    {
        if ($value) {
            $assurance = Assurance::find($value);
            if ($assurance) {
                $this->taux_couverture = $assurance->taux_couverture_defaut;
            }
        } else {
            $this->taux_couverture = 0;
        }
    }

    public function updatedPoids() { $this->calculerIMC(); }
    public function updatedTaille() { $this->calculerIMC(); }

    public function calculerIMC()
    {
        if (is_numeric($this->poids) && is_numeric($this->taille) && $this->taille > 0) {
            $tailleMetres = $this->taille > 3 ? $this->taille / 100 : $this->taille;
            $this->imc = round($this->poids / ($tailleMetres * $tailleMetres), 1);
        } else {
            $this->imc = null;
        }
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

    /**
     * Ouvre la modale d'affichage de la fiche médicale du patient
     * en chargeant l'assurance et l'historique complet des visites
     */
    public function showPatient($id)
    {
        $this->selectedPatient = Patient::with(['assurance', 'visites.visiteur', 'visites.agent'])
            ->findOrFail($id);
        $this->isViewModalOpen = true;
    }

    /**
     * Ferme la modale de consultation de fiche
     */
    public function closeViewModal()
    {
        $this->isViewModalOpen = false;
        $this->selectedPatient = null;
        $this->resetVisiteForm();
    }

    /**
     * Enregistre une visite directement depuis le dossier médical du patient
     */
    public function ajouterVisiteRapide()
    {
        $this->validate([
            'visiteur_nom' => 'required|string|max:255',
            'visiteur_telephone' => 'required|string|max:50',
            'visiteur_lien' => 'nullable|string|max:100',
            'chambre_lit' => 'nullable|string|max:100',
        ]);

        if (!$this->selectedPatient) return;

        // 1. Recherche ou création de l'identité du visiteur
        $visiteur = Visiteur::firstOrCreate(
            ['telephone' => $this->visiteur_telephone],
            [
                'nom_complet' => $this->visiteur_nom,
                'cni_ou_piece' => $this->visiteur_cni,
                'lien_parente' => $this->visiteur_lien,
            ]
        );

        // Mettre à jour l'identité si les données ont évolué
        $visiteur->update([
            'nom_complet' => $this->visiteur_nom,
            'cni_ou_piece' => $this->visiteur_cni ?: $visiteur->cni_ou_piece,
            'lien_parente' => $this->visiteur_lien ?: $visiteur->lien_parente,
        ]);

        // 2. Création de l'enregistrement de visite
        Visite::create([
            'code_visite' => 'VIS-' . date('Y') . '-' . strtoupper(uniqid()),
            'patient_id' => $this->selectedPatient->id,
            'visiteur_id' => $visiteur->id,
            'user_id' => auth()->id(),
            'date_heure_entree' => now(),
            'chambre_lit' => $this->chambre_lit,
            'badge_numero' => $this->badge_numero,
            'observations' => $this->observations,
        ]);

        // Recharger le modèle sélectionné et sa relation
        $this->selectedPatient->refresh();

        // Réinitialiser le formulaire de visite
        $this->resetVisiteForm();

        session()->flash('message_visite', 'Visite enregistrée avec succès.');
    }

    /**
     * Marquer la sortie du visiteur directement depuis la liste
     */
    public function marquerSortieVisite($visiteId)
    {
        $visite = Visite::findOrFail($visiteId);
        $visite->update([
            'date_heure_sortie' => now(),
        ]);

        if ($this->selectedPatient) {
            $this->selectedPatient->refresh();
        }
    }

    private function resetVisiteForm()
    {
        $this->visiteur_nom = '';
        $this->visiteur_telephone = '';
        $this->visiteur_lien = '';
        $this->visiteur_cni = '';
        $this->chambre_lit = '';
        $this->badge_numero = '';
        $this->observations = '';
    }

    public function resetForm()
    {
        $this->patient_id = null;
        $this->nom = '';
        $this->prenom = '';
        $this->genre = 'M';
        $this->date_naissance = '';
        $this->lieu_naissance = '';
        $this->lieu_residence = '';
        $this->profession = '';
        $this->religion = '';

        $this->est_assure = false;
        $this->assurance_id = null;
        $this->nom_assure = '';
        $this->matricule_assurance = '';
        $this->taux_couverture = 0;

        $this->poids = '';
        $this->taille = '';
        $this->temperature = '';
        $this->tension = '';
        $this->pouls = '';
        $this->spo2 = '';
        $this->imc = '';

        $this->telephone = '';
        $this->telephone_whatsapp = '';
        $this->email = '';
        $this->adresse = '';
        $this->ville = 'Douala';
        $this->groupe_sanguin = '';
        $this->allergies = '';
        $this->antecedents_medicaux = '';
        $this->resetValidation();
    }
public function savePatient()
    {
        $validatedData = $this->validate();

        // 1. Traitement des champs optionnels pour éviter les erreurs SQL (date, groupe sanguin, foreign keys...)
        $validatedData['date_naissance'] = !empty($this->date_naissance) ? $this->date_naissance : null;
        $validatedData['groupe_sanguin'] = !empty($this->groupe_sanguin) ? $this->groupe_sanguin : null;
        $validatedData['assurance_id']   = !empty($this->assurance_id) ? $this->assurance_id : null;

        // 2. Traitement des chaînes optionnelles pouvant arriver vides
        $validatedData['prenom']               = $this->prenom ?: null;
        $validatedData['lieu_naissance']        = $this->lieu_naissance ?: null;
        $validatedData['lieu_residence']        = $this->lieu_residence ?: null;
        $validatedData['profession']            = $this->profession ?: null;
        $validatedData['religion']              = $this->religion ?: null;
        $validatedData['telephone_whatsapp']    = $this->telephone_whatsapp ?: null;
        $validatedData['email']                 = $this->email ?: null;
        $validatedData['adresse']               = $this->adresse ?: null;
        $validatedData['ville']                 = $this->ville ?: null;
        $validatedData['allergies']             = $this->allergies ?: null;
        $validatedData['antecedents_medicaux']  = $this->antecedents_medicaux ?: null;

        // 3. Réinitialisation explicite si le patient n'est pas assuré
        if (!$this->est_assure) {
            $validatedData['assurance_id']        = null;
            $validatedData['nom_assure']          = null;
            $validatedData['matricule_assurance'] = null;
            $validatedData['taux_couverture']     = 0;
        } else {
            $validatedData['nom_assure']          = $this->nom_assure ?: null;
            $validatedData['matricule_assurance'] = $this->matricule_assurance ?: null;
            $validatedData['taux_couverture']     = is_numeric($this->taux_couverture) ? $this->taux_couverture : 0;
        }

        // 4. Structuration JSON des paramètres vitaux
        $validatedData['parametres'] = [
            'poids'       => $this->poids,
            'taille'      => $this->taille,
            'temperature' => $this->temperature,
            'tension'     => $this->tension,
            'pouls'       => $this->pouls,
            'spo2'        => $this->spo2,
            'imc'         => $this->imc,
        ];

        // 5. Génération automatique du code patient en création si absent
        if (!$this->isEditMode && empty($validatedData['code_patient'])) {
            $validatedData['code_patient'] = 'PAT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
        }

        // Enregistrement / Mise à jour
        Patient::updateOrCreate(['id' => $this->patient_id], $validatedData);

        session()->flash('message', $this->isEditMode ? 'Dossier patient mis à jour avec succès.' : 'Patient enregistré avec succès.');

        $this->closeModal();
    }

    // Ajoutez cette méthode dans la classe GestionPatients

    /**
     * Recherche automatique du visiteur dès la saisie de son numéro de téléphone
     */
    public function updatedVisiteurTelephone($value)
    {
        // Nettoyage de la chaîne tapée
        $telephoneClean = trim($value);

        if (strlen($telephoneClean) >= 8) {
            $visiteur = \App\Models\Visiteur::where('telephone', $telephoneClean)->first();

            if ($visiteur) {
                $this->visiteur_nom = $visiteur->nom_complet;
                $this->visiteur_cni = $visiteur->cni_ou_piece;
                $this->visiteur_lien = $visiteur->lien_parente;
                
                session()->flash('info_visiteur_trouve', 'Visiteur habituel identifié : ' . $visiteur->nom_complet);
            }
        }
    }

    public function editPatient($id)
    {
        $patient = Patient::findOrFail($id);
        $this->patient_id = $patient->id;
        $this->nom = $patient->nom;
        $this->prenom = $patient->prenom;
        $this->genre = $patient->genre;
        $this->date_naissance = $patient->date_naissance?->format('Y-m-d');
        $this->lieu_naissance = $patient->lieu_naissance;
        $this->lieu_residence = $patient->lieu_residence;
        $this->profession = $patient->profession;
        $this->religion = $patient->religion;

        // Assurance
        $this->est_assure = (bool) $patient->est_assure;
        $this->assurance_id = $patient->assurance_id;
        $this->nom_assure = $patient->nom_assure;
        $this->matricule_assurance = $patient->matricule_assurance;
        $this->taux_couverture = $patient->taux_couverture ?? 0;

        // Constantes vitaux depuis le champ JSON
        $params = $patient->parametres ?? [];
        $this->poids = $params['poids'] ?? '';
        $this->taille = $params['taille'] ?? '';
        $this->temperature = $params['temperature'] ?? '';
        $this->tension = $params['tension'] ?? '';
        $this->pouls = $params['pouls'] ?? '';
        $this->spo2 = $params['spo2'] ?? '';
        $this->imc = $params['imc'] ?? '';

        $this->telephone = $patient->telephone;
        $this->telephone_whatsapp = $patient->telephone_whatsapp;
        $this->email = $patient->email;
        $this->adresse = $patient->adresse;
        $this->ville = $patient->ville;
        $this->groupe_sanguin = $patient->groupe_sanguin;
        $this->allergies = $patient->allergies;
        $this->antecedents_medicaux = $patient->antecedents_medicaux;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function deletePatient($id)
    {
        Patient::findOrFail($id)->delete();
        session()->flash('message', 'Dossier patient archivé avec succès.');
    }

    public function render()
    {
        $patients = Patient::query()
            ->with('assurance')
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('telephone', 'like', '%' . $this->search . '%')
                      ->orWhere('code_patient', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtreGenre, function ($query) {
                $query->where('genre', $this->filtreGenre);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Liste dynamique des compagnies actives pour le selecteur d'assurance
        $assurancesList = Assurance::where('est_actif', true)->orderBy('nom', 'asc')->get();

        return view('livewire.patients.gestion-patients', compact('patients', 'assurancesList'));
    }
}