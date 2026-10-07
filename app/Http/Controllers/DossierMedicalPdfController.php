<?php

namespace App\Http\Controllers;

use App\Models\DossierMedical;
use App\Models\Consultation;

class DossierMedicalPdfController extends Controller
{
    public function genererRapportPdf(DossierMedical $dossier, $consultationId = null)
    {
        $dossier->load([
            'patient.assurance',
            'consultations.medecin',
            'consultations.demandesExamens.examen',
            'consultations.produits',
            'hospitalisations',
        ]);

        // Récupère la consultation ciblée par l'ID ou, par défaut, la plus récente
        $consultation = $consultationId 
            ? $dossier->consultations->find($consultationId) 
            : $dossier->consultations->sortByDesc('created_at')->first();

        return view('pdf.rapport-medical', [
            'dossier' => $dossier,
            'patient' => $dossier->patient,
            'consultation' => $consultation,
            'derniereConsultation' => $consultation, // Rétrocompatibilité avec vos vues existantes
        ]);
    }
}