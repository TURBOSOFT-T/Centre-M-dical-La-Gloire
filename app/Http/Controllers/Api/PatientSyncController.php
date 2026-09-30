<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientSyncController extends Controller
{
    public function sync(Request $request)
    {
        $localPatients = $request->input('patients', []);
        $lastSyncTime = $request->input('last_sync_time'); // Date de la dernière synchro du client

        $processedUuids = [];

        // 1. TRAITEMENT : Ce que le local envoie au serveur en ligne
        foreach ($localPatients as $data) {
            // Sécurité : Si l'uuid est absent, on en génère un
            $uuid = $data['uuid'] ?? (string) Str::uuid();

            $patient = Patient::where('uuid', $uuid)->first();

            $payload = [
                'uuid' => $uuid,
                'code_patient' => $data['code_patient'] ?? ('PAT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5))),
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
                'telephone' => $data['telephone'] ?? null, // Sécurisé en optionnel si besoin
                'telephone_whatsapp' => $data['telephone_whatsapp'] ?? null,
                'email' => $data['email'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'ville' => $data['ville'] ?? 'Douala',
                'groupe_sanguin' => $data['groupe_sanguin'] ?? null,
                'allergies' => $data['allergies'] ?? null,
                'antecedents_medicaux' => $data['antecedents_medicaux'] ?? null,
                'est_actif' => $data['est_actif'] ?? true,
                'updated_at' => $data['updated_at'] ?? now(),
            ];

            if ($patient) {
                // Règle du plus récent : si la modif locale est plus récente que la distante, on écrase
                if (isset($data['updated_at']) && strtotime($data['updated_at']) >= strtotime($patient->updated_at)) {
                    $patient->update($payload);
                }
            } else {
                // Le patient n'existe pas en ligne, on le crée
                Patient::create($payload);
            }

            $processedUuids[] = $uuid;
        }

        // 2. RÉPONSE : Le serveur envoie au local tout ce qui a changé depuis la dernière synchro
        $query = Patient::query();
        if ($lastSyncTime) {
            // On récupère tout ce qui a été modifié après la dernière synchro du client
            $query->where('updated_at', '>', $lastSyncTime);
        }
        
        $serverPatients = $query->get();

        return response()->json([
            'success' => true,
            'patients' => $serverPatients,
            'server_time' => now()->toIso8601String(), // Horodatage précis pour la prochaine fois
        ]);
    }
}