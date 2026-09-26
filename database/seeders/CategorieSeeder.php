<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Antalgiques & Antipyretiques',
                'description' => 'Médicaments pour le traitement de la douleur et de la fièvre (Paracétamol, Ibuprofène, Aspirine...).',
                'photo' => null,
            ],
            [
                'nom' => 'Antibiotiques & Antifongiques',
                'description' => 'Traitements des infections bactériennes et mycosiques.',
                'photo' => null,
            ],
            [
                'nom' => 'Anti-infectieux & Antipaludéens',
                'description' => 'Traitements contre le paludisme, les parasites et amibes.',
                'photo' => null,
            ],
            [
                'nom' => 'Anti-inflammatoires',
                'description' => 'Médicaments stéroïdiens et non stéroïdiens contre l\'inflammation.',
                'photo' => null,
            ],
            [
                'nom' => 'Cardiologie & Hypertension',
                'description' => 'Traitements des pathologies cardiovasculaires, de la tension artérielle et du cholestérol.',
                'photo' => null,
            ],
            [
                'nom' => 'Vitamines & Compléments Alimentaires',
                'description' => 'Suppléments nutritionnels, oligo-éléments et fortifiants.',
                'photo' => null,
            ],
            [
                'nom' => 'Matériel Médical & Pansements',
                'description' => 'Seringues, bandes, compresses, stéthoscopes, tensiomètres et consommables.',
                'photo' => null,
            ],
            [
                'nom' => 'Pédiatrie & Soins Bébé',
                'description' => 'Soins du nourrisson, laits infantiles, biberons et sirop pédiatriques.',
                'photo' => null,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['nom' => $cat['nom']],
                $cat
            );
        }
    }
}