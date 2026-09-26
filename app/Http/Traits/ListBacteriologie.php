<?php

namespace App\Http\Traits;

trait ListBacteriologie
{
    public function getListBacteriologie()
    {
        $examens = [
            ["Analyse" => "Chlamydia direct", "Prix" => "5000F"],
            ["Analyse" => "Compte d'ADDIS", "Prix" => "43000F"],
            ["Analyse" => "Coproculture", "Prix" => "13000F"],
            ["Analyse" => "ECBU", "Prix" => "3000F"],
            ["Analyse" => "ECBU + ATB", "Prix" => "12000F"],
            ["Analyse" => "Bordeaux", "Prix" => "#800000"],
            ["Analyse" => "Camel", "Prix" => "#C19A6B"],
            ["Analyse" => "Corail", "Prix" => "#FF7F50"],
            ["Analyse" => "Doré", "Prix" => "#FFD700"],
            ["Analyse" => "Fuchsia", "Prix" => "#FF00FF"],
            ["Analyse" => "Gris", "Prix" => "#808080"],
            ["Analyse" => "Jaune", "Prix" => "#FFFF00"],
            ["Analyse" => "Kaki", "Prix" => "#C3B091"],
            ["Analyse" => "Noir", "Prix" => "#000000"],
            ["Analyse" => "Nude", "Prix" => "#F5DEB3"],
            ["Analyse" => "Orange", "Prix" => "#FFA500"],
            ["Analyse" => "Rose", "Prix" => "#FFC0CB"],
            ["Analyse" => "Rouge", "Prix" => "#FF0000"],
            ["Analyse" => "Taupe", "Prix" => "#483C32"],
            ["Analyse" => "Turquoise", "Prix" => "#40E0D0"],
            ["Analyse" => "Vert", "Prix" => "#008000"],
            ["Analyse" => "Violet", "Prix" => "#800080"],
            ["Analyse" => "Multicolore", "Prix" => null],
        ];

        return collect($examens)->sortBy('Analyse')->values();
    }
}
