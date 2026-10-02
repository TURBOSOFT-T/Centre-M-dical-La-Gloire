<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Affiche la page des statistiques et rapports des consultations.
     */
    public function stats()
    {
        // Vous pouvez passer des données supplémentaires ici si nécessaire,
        // mais Livewire gère la logique de manière autonome.
        return view('admin.consultations.stats');
    }
}