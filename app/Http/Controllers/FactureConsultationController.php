<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class FactureConsultationController extends Controller
{
    /**
     * Génère et affiche le reçu / facture de la consultation en PDF.
     *
     * @param int $id
     * @return Response
     */
    public function imprimerFacture($id)
    {
        $consultation = Consultation::with([
            'patient.assurance',
            'medecin',
            'demandesExamens.examen',
        ])->findOrFail($id);

        // Calculs financiers
        $tarifConsultation = floatval($consultation->tarif_brut ?? 5000);
        $tarifExamens = floatval($consultation->demandesExamens->sum('tarif_brut'));
        $totalBrut = $tarifConsultation + $tarifExamens;

        $tauxAssurance = ($consultation->patient?->est_assure && $consultation->patient?->assurance)
            ? floatval($consultation->patient->taux_couverture ?? 0)
            : 0;

        $partAssurance = round(($totalBrut * $tauxAssurance) / 100);
        $partPatient = $totalBrut - $partAssurance;

        $pdf = Pdf::loadView('pdf.facture-consultation', compact(
            'consultation',
            'tarifConsultation',
            'tarifExamens',
            'totalBrut',
            'tauxAssurance',
            'partAssurance',
            'partPatient'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Facture_' . $consultation->code_consultation . '.pdf');
    }
}