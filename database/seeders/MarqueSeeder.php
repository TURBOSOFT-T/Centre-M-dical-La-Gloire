<?php

namespace Database\Seeders;

use App\Models\Marque;
use Illuminate\Database\Seeder;

class MarqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marques = [
            ['nom' => 'Pfizer', 'image' => null],
            ['nom' => 'Sanofi', 'image' => null],
            ['nom' => 'Novartis', 'image' => null],
            ['nom' => 'Roche', 'image' => null],
            ['nom' => 'AstraZeneca', 'image' => null],
            ['nom' => 'GlaxoSmithKline (GSK)', 'image' => null],
            ['nom' => 'Bayer', 'image' => null],
            ['nom' => 'Johnson & Johnson', 'image' => null],
        ];

        foreach ($marques as $marque) {
            Marque::updateOrCreate(
                ['nom' => $marque['nom']],
                ['image' => $marque['image']]
            );
        }
    }
}