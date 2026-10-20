<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Hospitalisation; // <-- Import du modèle Hospitalisation
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

        // 1. Calculs des frais de consultation et des examens
        $tarifConsultation = floatval($consultation->tarif_brut ?? 5000);
        $tarifExamens = floatval($consultation->demandesExamens->sum('tarif_brut'));

        // 2. Récupération de la dernière hospitalisation du patient
        $derniereHospitalisation = Hospitalisation::where('patient_id', $consultation->patient_id)
            ->latest('created_at')
            ->first();

        // Calcul du montant de l'hospitalisation si elle existe
        $tarifHospitalisation = $derniereHospitalisation ? $derniereHospitalisation->calculerMontantTotal() : 0;

        // 3. Total brut global incluant consultation, examens et dernière hospitalisation
        $totalBrut = $tarifConsultation + $tarifExamens + $tarifHospitalisation;

        // 4. Calcul de l'assurance et de la part patient
        $tauxAssurance = ($consultation->patient?->est_assure && $consultation->patient?->assurance)
            ? floatval($consultation->patient->taux_couverture ?? 0)
            : 0;

        $partAssurance = round(($totalBrut * $tauxAssurance) / 100);
        $partPatient = $totalBrut - $partAssurance;

        // 5. Génération du PDF
        $pdf = Pdf::loadView('pdf.facture-consultation', compact(
            'consultation',
            'tarifConsultation',
            'tarifExamens',
            'derniereHospitalisation', // Transmis pour affichage des détails si besoin
            'tarifHospitalisation',    // Transmis pour le montant
            'totalBrut',
            'tauxAssurance',
            'partAssurance',
            'partPatient'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Facture_' . ($consultation->code_consultation ?? $consultation->id) . '.pdf');
    }
}