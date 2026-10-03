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
        // Ajout de 'produits' dans le eager loading pour récupérer les médicaments prescrits
        $consultation = Consultation::with(['patient', 'medecin', 'demandesExamens', 'produits'])->findOrFail($id);

        // Calculs unifiés pour s'assurer que le PDF affiche les montants corrects
        $tarifConsultation = $consultation->tarif_brut ?? 5000;
        $tarifExamens = $consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0;
        
        $tarifProduits = 0;
        foreach ($consultation->produits as $prod) {
            $qte = $prod->pivot->quantite ?? 1;
            $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
            $tarifProduits += ($qte * $pu);
        }

        $totalBrut = $tarifConsultation + $tarifExamens + $tarifProduits;
        $totalFacture = $totalBrut;

        if ($consultation->patient && $consultation->patient->est_assure && $consultation->patient->assurance) {
            $taux = (float) $consultation->patient->taux_couverture;
            $partAssurance = round(($totalBrut * $taux) / 100);
            $totalFacture = $totalBrut - $partAssurance;
        }

        $montantPaye = $consultation->montant_paye ?? 0;
        $resteAPayer = max(0, $totalFacture - $montantPaye);

        return view('consultations.recu', compact(
            'consultation', 
            'tarifConsultation', 
            'tarifExamens', 
            'tarifProduits', 
            'totalBrut', 
            'totalFacture', 
            'montantPaye', 
            'resteAPayer'
        ));
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