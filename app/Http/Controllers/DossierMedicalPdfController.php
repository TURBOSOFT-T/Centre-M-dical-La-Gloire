<?php

namespace App\Http\Controllers;

use App\Models\DossierMedical;

class DossierMedicalPdfController extends Controller
{
    public function genererRapportPdf(DossierMedical $dossier)
    {
        $dossier->load([
            'patient.assurance',
            'consultations.medecin',
            'consultations.demandesExamens.examen',
            'hospitalisations',
        ]);

        return view('pdf.rapport-medical', [
            'dossier' => $dossier,
            'patient' => $dossier->patient,
        ]);
    }
}