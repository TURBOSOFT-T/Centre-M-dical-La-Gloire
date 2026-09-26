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
                'nom' => 'BACTERIOLOGIE',
                'caracteristiques' => [
                    ['nom' => 'CHLAMYDIA DIRECT', 'prix' => 5000],
                    ['nom' => 'COMPTED\'ADDIS', 'prix' => 43000],
                    ['nom' => 'COPROCULTURE', 'prix' => 13000],
                    ['nom' => 'ECBU', 'prix' => 3000],
                    ['nom' => 'ECBU + ATB', 'prix' => 12000],
                    ['nom' => 'EXAMEN CYTOBACTERIO DES EXPECTORATIONS + ATB', 'prix' => 11000],
                    ['nom' => 'HEMOCULTURE', 'prix' => 15000],
                    ['nom' => 'LCR + ATB', 'prix' => 15000],
                    ['nom' => 'LIQUIDE DE PONCTION +CHIMIE + ATB', 'prix' => 15000],
                    ['nom' => 'SANG OGULE(Recherche de sang dans les selles)', 'prix' => 6500],
                    ['nom' => 'MYCOPLASM DIRECT + ATB', 'prix' => 10000],
                    ['nom' => 'PCV + ATB ', 'prix' => 12000],
                    ['nom' => 'PCV SIMPLE', 'prix' => 3000],
                    ['nom' => 'PU + ATB', 'prix' => 12000],
                    ['nom' => 'PU SIMPLE', 'prix' => 3000],
                    ['nom' => 'PRELEVEMENT ORL + ATB', 'prix' => 14500],
                    ['nom' => 'PUS + ATB', 'prix' => 12000],
                    ['nom' => 'SPERMOGRAMME', 'prix' => 8000],
                    ['nom' => 'SPERMOCULTURE', 'prix' => 12000],
                    ['nom' => 'TEST TUMMER', 'prix' => 15000],
                    ['nom' => 'EXAMEN BACTERIOLOGIQUE DU PRELEVEMENT DE LA GORGE', 'prix' => 9500],
                    ['nom' => 'RECHERCHER DES BAAR 1,2,3', 'prix' => 9700],

                ],
            ],
            [
                'nom' => 'PARASITOLOGIE',
                'caracteristiques' => [

                    ['nom' => 'FILAIRE SANGUIN(frottis)', 'prix' => 2000],
                    ['nom' => 'GOUTTE EPAISSE', 'prix' => 1000],
                    ['nom' => 'HEMOPARASITE', 'prix' => 2000],

                    ['nom' => 'SCOTH TEST', 'prix' => 1000],
                    ['nom' => 'SELLES KOAP', 'prix' => 500],
                    ['nom' => 'SKINN SNIP', 'prix' => 2000],
                ],
            ],
            [
                'nom' => 'IMMUNO-SEROLOGIE',
                'caracteristiques' => [
                    ['nom' => 'ANTICORPS ANT-DELTA', 'prix' => 20000],
                    ['nom' => 'ANTICORPS ANTI HEPATHTE A IgG/IgM', 'prix' => 170000],
                    ['nom' => 'ANTICORPS ANTHCV ELISA', 'prix' => 17000],
                    ['nom' => 'ANTICORPS ANTI HEPATITE C:ACHCV', 'prix' => 15000],
                    ['nom' => 'AgHBS', 'prix' => 3000],
                    ['nom' => 'Ag H-PYLORI', 'prix' => 3000],
                    ['nom' => 'TPHA/HDRL', 'prix' => 3000],
                    ['nom' => 'CHLAMYDIA ELISA', 'prix' => 15000],
                ],
            ],


            [
                'nom' => 'HEMATOLOGIE',
                'caracteristiques' => [
                    ['nom' => 'ANTITROMBINE', 'prix' => 12000],
                    ['nom' => 'COMB DIRECT', 'prix' => 8000],
                    ['nom' => 'COMB INDIRECT', 'prix' => 7000],
                    ['nom' => 'D-DIMERE', 'prix' => 15500],
                    ['nom' => 'ELECTROPHORESE', 'prix' => 10000],
                    ['nom' => 'PROTTIS SANGUIN', 'prix' => 4000],
                    ['nom' => 'GROUPE SANGUIN', 'prix' => 2000],
                    ['nom' => 'NFS', 'prix' => 4000],
                    ['nom' => 'TAUS DE CD4', 'prix' => 11800],
                    ['nom' => 'TEMPS DE CEPHALINE KAOLIN TCK', 'prix' => 4000],
                    ['nom' => 'TC', 'prix' => 2000],
                    ['nom' => 'TS', 'prix' => 2000],
                    ['nom' => 'TEST D\'EMMEL', 'prix' => 4000],
                    ['nom' => 'V/S(VITESSE DE SEDIMENTION)', 'prix' => 2000],
                    ['nom' => 'TAUX DE PROTHOMBINE TP', 'prix' => 4000],
                    ['nom' => 'ANTICORPS ANTI HEPATHTE A IgG/IgM', 'prix' => 170000],
                ],
            ],
            [
                'nom' => 'BIOCHIMIE',
                'caracteristiques' => [
                    ['nom' => 'ACIDE URIQUE', 'prix' => 3000],
                    ['nom' => 'CALCIUM', 'prix' => 3000],
                    ['nom' => 'CHLORE', 'prix' => 3000],
                    ['nom' => 'CHLOEESTEROL HDL', 'prix' => 3000],
                    ['nom' => 'CHLOLESTEROL LDL', 'prix' => 3000],
                    ['nom' => 'CREATINNE', 'prix' => 3000],
                    ['nom' => 'CHLOLEROL TOTAL', 'prix' => 3000],
                    ['nom' => 'GLYCEMIE PP', 'prix' => 500],
                    ['nom' => 'ELECTREPHORESE DES PROTEINES', 'prix' => 22000],
                    ['nom' => 'HBAC(Hémoglobine glyquée)', 'prix' => 10000],
                    ['nom' => 'IONGRAMME ca+, mg+, k+, na+, cl-', 'prix' => 15000],
                    ['nom' => 'MAGNESIUM', 'prix' => 3000],
                    ['nom' => 'NATREMIE', 'prix' => 3000],
                    ['nom' => 'PROFIL LIPIDIQUE TG, CHLT, LDL, HDL', 'prix' => 12000],
                    ['nom' => 'PROTEIN TOTAL', 'prix' => 7500],
                    ['nom' => 'SGOT ASAT', 'prix' => 4000],
                    ['nom' => 'SGP ALAT', 'prix' => 4000],
                    ['nom' => 'TRIGLICERIDE', 'prix' => 3000],
                    ['nom' => 'UREE', 'prix' => 3000],
                    ['nom' => 'GLUCOSE', 'prix' => 3000],
                ],
            ],
            [
                'nom' => 'BICHIMIE URINAIRE',
                'caracteristiques' => [
                    ['nom' => 'ALBUMIN/SUCRE', 'prix' => 1500],
                    ['nom' => 'BANDELLETTE URINAIRE BU', 'prix' => 1500],
                    ['nom' => 'GLUCOSE URINAIRE', 'prix' => 1000],
                    ['nom' => 'PROTEIN DE 24H', 'prix' => 6500],
                    ['nom' => 'TEST URINAIRE', 'prix' => 500],
                ],
            ],
            [
                'nom' => 'HORMONES',
                'caracteristiques' => [
                    ['nom' => 'BETA -HCG QUANTITATIF', 'prix' => 13500],
                    ['nom' => 'CORTISOL', 'prix' => 31000],
                    ['nom' => 'FSH', 'prix' => 12000],
                    ['nom' => 'TESTOSTERONE', 'prix' => 13000],
                    ['nom' => 'LH', 'prix' => 12000],

                    ['nom' => 'OSTRADIOLEMIE', 'prix' => 13000],
                    ['nom' => 'PARATHORMONE PTH', 'prix' => 20000],
                    ['nom' => 'PROGESTERONE', 'prix' => 13000],
                    ['nom' => 'PROLACTINE', 'prix' => 13000],
                    ['nom' => 'TEST D\'OVULATION', 'prix' => 10000],
                    ['nom' => 'TSH', 'prix' => 13000],
                    ['nom' => 'THYROXINE T4', 'prix' => 13000],
                    ['nom' => 'TRIODOTHYRONINE T3', 'prix' => 13000],
                ],
            ],
            [
                'nom' => 'MARQUEURS TUMORAUX',
                'caracteristiques' => [
                    ['nom' => 'ALPHA FOETOPROTEIN', 'prix' => 17000],
                    ['nom' => 'ANTIGEN CARCINO EMBRYONNAIRE', 'prix' => 17500],
                    ['nom' => 'PSA LIBRE', 'prix' => 12000],
                    ['nom' => 'PSA TOTAL', 'prix' => 12000],
                ],
            ],
            [
                'nom' => 'BIOLOGIE MOLECULAIRE',
                'caracteristiques' => [
                    ['nom' => 'CHARGE VIRALE VIH', 'prix' => 30000],
                    ['nom' => 'CHARGE VIRALE HEPATITE B', 'prix' => 48000],
                    ['nom' => 'CHARGE VIRALE HEPATITE C', 'prix' => 87000],
                    ['nom' => 'PCR CHLAMYDIA', 'prix' => 45000],
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
