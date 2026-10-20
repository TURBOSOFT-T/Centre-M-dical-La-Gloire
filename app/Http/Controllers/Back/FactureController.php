<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    /**
     * Génère la facture PDF spécifique aux médicaments/produits prescrits.
     */
    public function genererFactureMedicaments($id)
    {
        $consultation = Consultation::with(['patient.assurance', 'produits', 'medecin'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.facture-medicaments', compact('consultation'));
        return $pdf->stream('Facture_Medicaments_' . $consultation->code_consultation . '.pdf');
    }

    /**
     * Génère la facture PDF spécifique aux examens de laboratoire / imagerie.
     */
    public function genererFactureExamens($id)
    {
        $consultation = Consultation::with(['patient.assurance', 'demandesExamens', 'medecin'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.facture-examens', compact('consultation'));
        return $pdf->stream('Facture_Examens_' . $consultation->code_consultation . '.pdf');
    }
/**
 * Génère la facture PDF récapitulative globale (Consultation + Examens + Médicaments).
 */
public function genererFactureGlobale($id)
{
    $consultation = Consultation::with([
        'patient.assurance',
        'patient.hospitalisations', // Charger toutes les hospitalisations pour extraire la dernière
        'medecin',
        'demandesExamens',
        'produits',
        'hospitalisation'
    ])->findOrFail($id);

    // 1. Consultation
    $tarifConsultation = $consultation->tarif_brut ?? 5000;

    // 2. Examens
    $tarifExamens = $consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0;

    // 3. Produits & Médicaments
    $tarifProduits = 0;
    if ($consultation->produits) {
        foreach ($consultation->produits as $prod) {
            $qte = $prod->pivot->quantite ?? 1;
            $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
            $tarifProduits += ($qte * $pu);
        }
    }

    // 4. Parcourir et récupérer la dernière hospitalisation du patient
    $derniereHospitalisation = $consultation->hospitalisation 
        ?? $consultation->patient?->hospitalisations()->latest('created_at')->first();

    $tarifHospitalisation = $derniereHospitalisation ? floatval($derniereHospitalisation->montant_total ?? 0) : 0;

    // 5. Total Général Brut
    $totalGeneral = $tarifConsultation + $tarifExamens + $tarifProduits + $tarifHospitalisation;
    $totalBrut = $totalGeneral;

    // 6. Prise en charge Assurance
    $tauxAssurance = ($consultation->patient && $consultation->patient->est_assure) 
        ? ($consultation->patient->taux_couverture ?? $consultation->patient->assurance?->taux ?? 0) 
        : 0;

    $partAssurance = round(($totalGeneral * $tauxAssurance) / 100);
    $partPatient = max(0, $totalGeneral - $partAssurance);
    $resteAEncaisser = max(0, $partPatient - ($consultation->montant_paye ?? 0));

    $pdf = Pdf::loadView('pdf.facture-globale', compact(
        'consultation',
        'derniereHospitalisation',
        'tarifConsultation',
        'tarifExamens',
        'tarifProduits',
        'tarifHospitalisation',
        'totalGeneral',
        'totalBrut',
        'tauxAssurance',
        'partAssurance',
        'partPatient',
        'resteAEncaisser'
    ))->setPaper('a4', 'portrait');

    return $pdf->stream('Facture_Globale_' . $consultation->code_consultation . '.pdf');
}
}