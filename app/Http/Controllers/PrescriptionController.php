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
        $consultation = Consultation::with(['patient.assurance', 'medecin', 'produits'])->findOrFail($id);

        if (empty($consultation->ordonnance) && ($consultation->produits->isEmpty())) {
            return back()->with('error', 'Aucune ordonnance ni produit n\'a été enregistré pour cette consultation.');
        }

        // Chargement de la vue Blade avec DomPDF
        $pdf = Pdf::loadView('pdf.ordonnance', compact('consultation'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream('Ordonnance_' . ($consultation->code_consultation ?? $id) . '.pdf');
    }
    public function imprimerFactureConsultation($id)
{
    $consultation = Consultation::with([
        'patient.assurance', 
        'medecin', 
        'demandesExamens', 
        'produits'
    ])->findOrFail($id);

    $tarifConsultation = $consultation->tarif_brut ?? 5000;
    $tarifExamens = $consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0;

    // --- VOTRE BLOC DE CALCUL ---
    $tarifProduits = 0;
    if ($consultation->produits) {
        foreach ($consultation->produits as $prod) {
            $qte = $prod->pivot->quantite ?? 1;
            $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
            $tarifProduits += ($qte * $pu);
        }
    }
    // ----------------------------

    $totalGeneral = $tarifConsultation + $tarifExamens + $tarifProduits;

    // Gestion assurance...
    $tauxAssurance = $consultation->patient && $consultation->patient->est_assure ? ($consultation->patient->taux_couverture ?? 0) : 0;
    $partAssurance = round(($totalGeneral * $tauxAssurance) / 100);
    $partPatient = $totalGeneral - $partAssurance;
    $resteAEncaisser = max(0, $partPatient - ($consultation->montant_paye ?? 0));

    // IL FAUT IMPÉRATIVEMENT METTRE 'tarifProduits' DANS LE COMPACT :
    $pdf = Pdf::loadView('pdf.facture_consultation', compact(
        'consultation',
        'tarifConsultation',
        'tarifExamens',
        'tarifProduits', // <--- Indispensable ici
        'totalGeneral',
        'tauxAssurance',
        'partAssurance',
        'partPatient',
        'resteAEncaisser'
    ))->setPaper('a5', 'portrait');

    return $pdf->stream('Facture_' . ($consultation->code_consultation ?? $id) . '.pdf');
}
}