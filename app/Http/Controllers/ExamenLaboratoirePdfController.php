<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ExamenLaboratoirePdfController extends Controller
{
    /**
     * Génère et affiche le bulletin de demande d'examens pour le laboratoire.
     *
     * @param int $consultationId
     * @return Response
     */
    public function imprimerBulletinLabo($consultationId)
    {
        $consultation = Consultation::with([
            'patient.dossierMedical',
            'medecin',
            'demandesExamens.examen',
            'demandesExamens.prescripteur',
        ])->findOrFail($consultationId);

        // Récupération de toutes les demandes d'examens liées à cette consultation
        $demandes = $consultation->demandesExamens;

        if ($demandes->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun examen de laboratoire n\'a été prescrit pour cette consultation.');
        }

        $pdf = Pdf::loadView('pdf.bulletin-laboratoire', compact(
            'consultation',
            'demandes'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Bulletin_Examens_Labo_' . $consultation->code_consultation . '.pdf');
    }
}