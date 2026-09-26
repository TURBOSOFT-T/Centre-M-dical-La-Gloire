<?php

namespace Database\Seeders;

use App\Models\Assurance;
use Illuminate\Database\Seeder;

class AssuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assurances = [
            [
                'code' => 'ASCOMA-CMR',
                'nom' => 'ASCOMA Cameroun',
                'telephone' => '+237 233 42 12 00',
                'email' => 'contact.cameroun@ascoma.com',
                'adresse' => 'Akwa, Douala',
                'taux_couverture_defaut' => 80,
                'est_actif' => true,
            ],
            [
                'code' => 'AXA-SANTE',
                'nom' => 'AXA Assurances Cameroun',
                'telephone' => '+237 233 43 50 00',
                'email' => 'sante@axa.cm',
                'adresse' => 'Bonanjo, Douala',
                'taux_couverture_defaut' => 80,
                'est_actif' => true,
            ],
            [
                'code' => 'SANLAM-SAHAM',
                'nom' => 'Sanlam Assurance (ex-SAHAM)',
                'telephone' => '+237 233 42 08 88',
                'email' => 'contact.cm@sanlam.com',
                'adresse' => 'Boulevard de la Liberté, Akwa, Douala',
                'taux_couverture_defaut' => 70,
                'est_actif' => true,
            ],
            [
                'code' => 'ALLIANZ-CMR',
                'nom' => 'Allianz Cameroun',
                'telephone' => '+237 233 42 55 55',
                'email' => 'info@allianz.cm',
                'adresse' => 'Rue Joffre, Akwa, Douala',
                'taux_couverture_defaut' => 80,
                'est_actif' => true,
            ],
            [
                'code' => 'ACTIVA-ASSUR',
                'nom' => 'Activa Assurances',
                'telephone' => '+237 233 43 20 00',
                'email' => 'contact@activa-assurances.com',
                'adresse' => 'Rue King Akwa, Douala',
                'taux_couverture_defaut' => 80,
                'est_actif' => true,
            ],
            [
                'code' => 'CHANAS-ASSUR',
                'nom' => 'Chanas Assurances',
                'telephone' => '+237 233 42 20 20',
                'email' => 'sante@chanasassurances.com',
                'adresse' => 'Bonanjo, Douala',
                'taux_couverture_defaut' => 80,
                'est_actif' => true,
            ],
            [
                'code' => 'CNPS-CMR',
                'nom' => 'Caisse Nationale de Prévoyance Sociale (CNPS)',
                'telephone' => '+237 222 23 40 40',
                'email' => 'contact@cnps.cm',
                'adresse' => 'Yaoundé / Douala',
                'taux_couverture_defaut' => 100,
                'est_actif' => true,
            ],
        ];

        foreach ($assurances as $data) {
            Assurance::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}