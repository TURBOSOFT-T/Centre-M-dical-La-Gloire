<?php

namespace Database\Seeders;

use App\Models\Examen;
use Illuminate\Database\Seeder;

class ExamenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examens = [
            [
                'nom' => '',
                'caracteristiques' => [
                    ['nom' => 'Hémoglobine (Hb)', 'prix' => 2000],
                    ['nom' => 'Hématocrite', 'prix' => 1500],
                    ['nom' => 'Globules Rouges (VGM)', 'prix' => 1500],
                    ['nom' => 'Globules Blancs (Leucocytes)', 'prix' => 2000],
                    ['nom' => 'Plaquettes', 'prix' => 2000],
                    ['nom' => 'Polynucléaires Neutrophiles', 'prix' => 1000],
                    ['nom' => 'Lymphocytes', 'prix' => 1000],
                ],
            ],
            [
                'nom' => 'Sérodiagnostic de Widal et Félix (Fièvre Typhoïde)',
                'caracteristiques' => [
                    ['nom' => 'Antigène O (Salmonella typhi)', 'prix' => 2500],
                    ['nom' => 'Antigène H (Salmonella typhi)', 'prix' => 2500],
                    ['nom' => 'Antigène AO (Paratyphi A)', 'prix' => 1500],
                    ['nom' => 'Antigène BO (Paratyphi B)', 'prix' => 1500],
                ],
            ],
            [
                'nom' => 'Goutte Épaisse & Frottis Sanguin (Paludisme)',
                'caracteristiques' => [
                    ['nom' => 'Recherche de Plasmodium', 'prix' => 2000],
                    ['nom' => 'Densité Parasitaire', 'prix' => 1000],
                ],
            ],
            [
                'nom' => 'Bilan Biochimique Renal & Glycémique',
                'caracteristiques' => [
                    ['nom' => 'Glycémie à jeun', 'prix' => 1500],
                    ['nom' => 'Urée sanguine', 'prix' => 2000],
                    ['nom' => 'Créatininémie', 'prix' => 2500],
                    ['nom' => 'Acide Urique', 'prix' => 2000],
                ],
            ],
            [
                'nom' => 'Bilan Lipidique',
                'caracteristiques' => [
                    ['nom' => 'Cholestérol Total', 'prix' => 2500],
                    ['nom' => 'HDL Cholestérol (Bon)', 'prix' => 3000],
                    ['nom' => 'LDL Cholestérol (Mauvais)', 'prix' => 3000],
                    ['nom' => 'Triglycérides', 'prix' => 2500],
                ],
            ],
            [
                'nom' => 'Groupage Sanguin & Facteur Rhésus',
                'caracteristiques' => [
                    ['nom' => 'Groupe Sanguin ABO', 'prix' => 2000],
                    ['nom' => 'Facteur Rhésus', 'prix' => 1000],
                ],
            ],
            [
                'nom' => 'Examen Cytobactériologique des Urines (ECBU)',
                'caracteristiques' => [
                    ['nom' => 'Aspect des Urines', 'prix' => 1000],
                    ['nom' => 'Leucocytes urinaire', 'prix' => 1500],
                    ['nom' => 'Hématies urinaire', 'prix' => 1500],
                    ['nom' => 'Culture bactérienne', 'prix' => 3000],
                ],
            ],
            [
                'nom' => 'Bilan Hépatique (Transaminases)',
                'caracteristiques' => [
                    ['nom' => 'ASAT (SGOT)', 'prix' => 2500],
                    ['nom' => 'ALAT (SGPT)', 'prix' => 2500],
                    ['nom' => 'Bilirubine Totale', 'prix' => 2500],
                    ['nom' => 'Phosphatases Alcalines (PAL)', 'prix' => 3000],
                ],
            ],
        ];

        foreach ($examens as $data) {
            Examen::updateOrCreate(
                ['nom' => $data['nom']],
                [
                    'caracteristiques' => $data['caracteristiques'],
                ]
            );
        }
    }
}