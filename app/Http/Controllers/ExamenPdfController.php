<?php

namespace App\Http\Controllers;

use App\Models\DemandeExamen;
use Illuminate\Http\Request;

class ExamenPdfController extends Controller
{
    public function genererPdf(DemandeExamen $demande)
    {
        $demande->load(['examen', 'patient', 'prescripteur', 'realisateur']);

        // Option A : Affichage de la vue d'impression HTML / Impression navigateur
        return view('pdf.examen-resultat', [
            'demande' => $demande
        ]);

        /* 
        // Option B : Si vous utilisez le package dompdf (barryvdh/laravel-dompdf)
        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.examen-resultat', compact('demande'));
        // return $pdf->stream("Resultats_Examen_{$demande->code_demande}.pdf");
        */
    }
}