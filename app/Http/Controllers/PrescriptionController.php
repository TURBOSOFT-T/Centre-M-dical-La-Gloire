<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Génère et affiche le PDF de l'ordonnance médicale
     */
    public function imprimerOrdonnance($id)
    {
        $consultation = Consultation::with(['patient.assurance', 'medecin'])->findOrFail($id);

        if (empty($consultation->ordonnance)) {
            return back()->with('error', 'Aucune ordonnance n\'a été rédigée pour cette consultation.');
        }

        // Chargement de la vue Blade avec DomPDF
        $pdf = Pdf::loadView('pdf.ordonnance', compact('consultation'))
            ->setPaper('a5', 'portrait'); // Format A5 standard pour ordonnance médicale (ou 'a4')

        // 'stream' permet de l'ouvrir directement dans le navigateur pour impression
        return $pdf->stream('Ordonnance_' . $consultation->code_consultation . '.pdf');
    }


    public function imprimerFactureConsultation($id)
    {
        $consultation = Consultation::with(['patient.assurance', 'medecin'])->findOrFail($id);

        // Calculs financiers pour le tiers-payant
        $tarifBrut = $consultation->tarif_brut ?? 5000;
        $tauxAssurance = 0;
        $partAssurance = 0;

        if ($consultation->patient && $consultation->patient->est_assure) {
            $tauxAssurance = $consultation->patient->taux_couverture ?? 0;
            $partAssurance = round(($tarifBrut * $tauxAssurance) / 100);
        }

        $partPatient = $tarifBrut - $partAssurance;

        $pdf = Pdf::loadView('pdf.facture_consultation', compact(
            'consultation',
            'tarifBrut',
            'tauxAssurance',
            'partAssurance',
            'partPatient'
        ))->setPaper('a5', 'portrait');

        return $pdf->stream('Facture_' . $consultation->code_consultation . '.pdf');
    }
}