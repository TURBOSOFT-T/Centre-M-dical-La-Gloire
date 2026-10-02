<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HospitalisationReportController extends Controller
{
    /**
     * Affiche la page des statistiques et rapports d'hospitalisation.
     */
    public function index()
    {
        return view('admin.hospitalisations.stats'); // Chemin vers la vue Blade parente
    }
}