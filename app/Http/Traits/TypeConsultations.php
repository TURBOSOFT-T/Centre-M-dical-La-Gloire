<?php

namespace App\Http\Traits;

trait TypeConsultations
{
    /**
     * Retourne la liste des types de consultations avec leurs prix par défaut.
     */
    public function getTypeConsultations(): array
    {
        return [
            'medecin_general' => ['nom' => 'Médecin général', 'prix' => 2500],
            'specialiste'     => ['nom' => 'Spécialiste', 'prix' => 10000],
            'suivi'           => ['nom' => 'Suivi', 'prix' => 3000],
            'urgence'         => ['nom' => 'Urgence', 'prix' => 15000],
            'sage_femme'      => ['nom' => 'Sage femme', 'prix' => 3500],
            'pediatrie'       => ['nom' => 'Pédiatrie', 'prix' => 6000],
            'cardiologie'     => ['nom' => 'Cardiologie', 'prix' => 12000],
            'dermatologie'    => ['nom' => 'Dermatologie', 'prix' => 5000],
            'gynecologie'     => ['nom' => 'Gynécologue', 'prix' => 10000],
            'neurologie'      => ['nom' => 'Neurologie', 'prix' => 15000],
            'ophtalmologie'   => ['nom' => 'Ophtalmologie', 'prix' => 4000],
            'orthopedie'      => ['nom' => 'Orthopédie', 'prix' => 12000],
            'psychiatrie'     => ['nom' => 'Psychiatrie', 'prix' => 15000],
            'radiologie'      => ['nom' => 'Radiologie', 'prix' => 20000],
            'chirurgie'       => ['nom' => 'Chirurgie', 'prix' => 10000],
            'dentisterie'     => ['nom' => 'Dentiste', 'prix' => 10000],
            'traumatologie'   => ['nom' => 'Traumatologie', 'prix' => 4000],
            'orl'             => ['nom' => 'ORL', 'prix' => 15000],
            'kinesitherapie'  => ['nom' => 'Kinésithérapie', 'prix' => 4000],
            'nutrition'       => ['nom' => 'Nutrition', 'prix' => 5000],
            'autre'           => ['nom' => 'Autre', 'prix' => 2500],
        ];
    }

    /**
     * Récupère le prix d'un type de consultation spécifique.
     */
    public function getPrixConsultation(string $type): float
    {
        $types = $this->getTypeConsultations();
        return $types[$type]['prix'] ?? 2500; // 2500 FCFA par défaut si non trouvé
    }
}