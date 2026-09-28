<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationRecuController extends Controller
{
    /**
     * Génère le reçu global ou partiel de la consultation
     */
    public function imprimerRecu($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'demandesExamens'])->findOrFail($id);

        // Vous pouvez retourner une vue Blade dédiée à l'impression (ex: resources/views/pdf/recu-caisse.blade.php)
        return view('consultations.recu', compact('consultation'));
    }

    /**
     * Génère le reçu pour une tranche spécifique de l'historique
     */
    public function imprimerRecuTranche($id, $index)
    {
        $consultation = Consultation::with(['patient', 'medecin'])->findOrFail($id);
        
        $historique = $consultation->historique_paiements ?? [];
        if (is_string($historique)) {
            $historique = json_decode($historique, true) ?? [];
        }

        $tranche = $historique[$index] ?? null;

        if (!$tranche) {
            abort(404, 'Tranche de paiement introuvable.');
        }

        return view('consultations.recu-tranche', compact('consultation', 'tranche', 'index'));
    }
}