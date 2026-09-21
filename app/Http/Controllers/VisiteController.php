<?php

namespace App\Http\Controllers;

use App\Models\Visite;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class VisiteController extends Controller
{
    public function imprimerPassPdf($id)
    {
        $visite = Visite::with(['patient', 'visiteur', 'agent'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.pass-visite', compact('visite'))
                  ->setPaper([0, 0, 226.77, 340.16], 'portrait'); // Format Ticket / Pass Billet (80mm x 120mm)

        return $pdf->stream('Pass-Visite-' . $visite->code_visite . '.pdf');
    }
}