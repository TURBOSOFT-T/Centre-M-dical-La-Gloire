<?php

namespace Database\Seeders;

use App\Models\Visiteur;
use Illuminate\Database\Seeder;

class VisiteurSeeder extends Seeder
{
    public function run(): void
    {
        $visiteurs = [
            ['nom_complet' => 'MAMPOUYA Jean-Paul', 'telephone' => '+237699112233', 'cni_ou_piece' => '102938475', 'lien_parente' => 'Époux'],
            ['nom_complet' => 'EBOKE Clarisse', 'telephone' => '+237677223344', 'cni_ou_piece' => '203948576', 'lien_parente' => 'Soeur'],
            ['nom_complet' => 'TCHATCHOU Bertrand', 'telephone' => '+237655334455', 'cni_ou_piece' => '304958677', 'lien_parente' => 'Frère'],
            ['nom_complet' => 'NANGO Martial', 'telephone' => '+237690445566', 'cni_ou_piece' => '405968778', 'lien_parente' => 'Père'],
        ];

        foreach ($visiteurs as $v) {
            Visiteur::updateOrCreate(['telephone' => $v['telephone']], $v);
        }
    }
}