<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientSyncController extends Controller
{
    public function sync(Request $request)
    {
        $patientsData = $request->input('patients', []);

        foreach ($patientsData as $data) {
            // updateOrCreate se base sur le 'uuid' unique. 
            // S'il existe en ligne, il est mis à jour. Sinon, il est créé.
            Patient::updateOrCreate(
                ['uuid' => $data['uuid']],
                [
                    'code_patient' => $data['code_patient'],
                    'assurance_id' => $data['assurance_id'] ?? null,
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'] ?? null,
                    'genre' => $data['genre'],
                    'date_naissance' => $data['date_naissance'] ?? null,
                    'lieu_naissance' => $data['lieu_naissance'] ?? null,
                    'lieu_residence' => $data['lieu_residence'] ?? null,
                    'profession' => $data['profession'] ?? null,
                    'religion' => $data['religion'] ?? null,
                    'est_assure' => $data['est_assure'] ?? false,
                    'nom_assure' => $data['nom_assure'] ?? null,
                    'matricule_assurance' => $data['matricule_assurance'] ?? null,
                    'taux_couverture' => $data['taux_couverture'] ?? 0,
                    'parametres' => $data['parametres'] ?? null,
                    'telephone' => $data['telephone'],
                    'telephone_whatsapp' => $data['telephone_whatsapp'] ?? null,
                    'email' => $data['email'] ?? null,
                    'adresse' => $data['adresse'] ?? null,
                    'ville' => $data['ville'] ?? 'Douala',
                    'groupe_sanguin' => $data['groupe_sanguin'] ?? null,
                    'allergies' => $data['allergies'] ?? null,
                    'antecedents_medicaux' => $data['antecedents_medicaux'] ?? null,
                    'est_actif' => $data['est_actif'] ?? true,
                    'is_synced' => true, // Sur le serveur distant, c'est forcément synchronisé
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Patients synchronisés avec succès sur le serveur distant.'
        ]);
    }
}