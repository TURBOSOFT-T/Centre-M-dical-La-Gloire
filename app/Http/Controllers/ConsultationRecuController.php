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
        // Ajout de 'commande.lignes.produit' et 'patient.assurance' dans le eager loading
        $consultation = Consultation::with([
            'patient.assurance', 
            'medecin', 
            'demandesExamens.examen', 
            'produits', 
            'commande.lignes.produit'
        ])->findOrFail($id);

        // Calculs unifiés pour s'assurer que le reçu affiche les montants corrects
        $tarifConsultation = $consultation->tarif_brut ?? 5000;
        $tarifExamens = $consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0;
        
        // 1. Produits prescrits directement (via relation pivot)
        $tarifProduitsPrescrits = 0;
        foreach ($consultation->produits as $prod) {
            $qte = $prod->pivot->quantite ?? 1;
            $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
            $tarifProduitsPrescrits += ($qte * $pu);
        }

        // 2. Produits issus d'une commande liée
        $tarifProduitsCommande = 0;
        $lignesCommande = collect();
        if ($consultation->commande && method_exists($consultation->commande, 'lignes')) {
            $lignesCommande = $consultation->commande->lignes;
        } elseif ($consultation->commande && $consultation->commande->relationLoaded('lignes')) {
            $lignesCommande = $consultation->commande->lignes;
        }

        foreach ($lignesCommande as $ligne) {
            $qteCmd = $ligne->quantite ?? 1;
            $puCmd = $ligne->prix_unitaire ?? $ligne->prix ?? 0;
            $tarifProduitsCommande += ($qteCmd * $puCmd);
        }

        // Total Brut global incluant tous les éléments
        $totalBrut = $tarifConsultation + $tarifExamens + $tarifProduitsPrescrits + $tarifProduitsCommande;
        $totalFacture = $totalBrut;

        // Gestion de l'assurance
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
            'tarifProduitsPrescrits', 
            'tarifProduitsCommande',
            'lignesCommande',
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
      //  $consultation = Consultation::with(['patient.assurance', 'medecin'])->findOrFail($id);
         $consultation = Consultation::with([
            'patient.assurance', 
            'medecin', 
            'demandesExamens.examen', 
            'produits', 
            'commande.lignes.produit'
        ])->findOrFail($id);
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