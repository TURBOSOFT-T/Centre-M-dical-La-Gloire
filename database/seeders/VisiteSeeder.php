<?php

namespace Database\Seeders;

use App\Models\Visite;
use App\Models\Patient;
use App\Models\Visiteur;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VisiteSeeder extends Seeder
{
    public function run(): void
    {
        $patient = Patient::first();
        $visiteur = Visiteur::first();
        $user = User::first(); // Récupère le premier utilisateur enregistré (Admin ou Secrétaire)

        if ($patient && $visiteur) {
            Visite::updateOrCreate(
                ['code_visite' => 'VIS-2026-0001'],
                [
                    'patient_id' => $patient->id,
                    'visiteur_id' => $visiteur->id,
                    'user_id' => $user?->id, // Sera nul si aucun utilisateur n'existe encore
                    'date_heure_entree' => Carbon::now()->subHours(2),
                    'date_heure_sortie' => null,
                    'chambre_lit' => 'Chambre 102 - Lit A',
                    'badge_numero' => 12,
                    'observations' => 'Dépôt de repas et effets personnels.',
                ]
            );
        }
    }
}