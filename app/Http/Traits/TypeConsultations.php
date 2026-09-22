<?php

namespace App\Http\Traits;

trait TypeConsultations
{

    public function  getTypeConsultations()
    {
        return [
            "Medecin general",
            "Specialiste",
            "Suivi",
            "Urgence",
            "Sage femme",
            "Pédiatrie",
            "Cardiologie",
            "Dermatologie",
            "Gynécologie",
            "Neurologie",
            "Ophtalmologie",
            "Orthopédie",
            "Psychiatrie",
            "Radiologie",
            "Chirurgie",
            "Dentisterie",
            "Tromatologie",
            "ORL",
            "Kinesithérapie",
            "Nutrition",
            "Autre"

        ];
    }
}
