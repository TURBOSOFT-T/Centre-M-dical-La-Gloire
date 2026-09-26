<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ResultatsExamenPdfController extends Controller
{
    /**
     * Génère et affiche le compte-rendu des résultats d'analyses en PDF.
     *
     * @param int $consultationId
     * @return Response
     */
    public function imprimerResultats($consultationId)
    {
        $consultation = Consultation::with([
            'patient.dossierMedical',
            'medecin',
            'demandesExamens.examen',
            'demandesExamens.prescripteur',
            'demandesExamens.realisateur',
        ])->findOrFail($consultationId);

        $demandes = $consultation->demandesExamens;

        if ($demandes->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun examen de laboratoire n\'est associé à cette consultation.');
        }

        $pdf = Pdf::loadView('pdf.resultats-analyses', compact(
            'consultation',
            'demandes'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Resultats_Analyses_' . $consultation->code_consultation . '.pdf');
    }
}