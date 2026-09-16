<?php

namespace App\Http\Traits;

trait ListColor
{
    public function getListColor()
    {
        $colors = [
            ["nom" => "Argenté", "code" => "#C0C0C0"],
            ["nom" => "Beige", "code" => "#F5F5DC"],
            ["nom" => "Blanc", "code" => "#FFFFFF"],
            ["nom" => "Bleu", "code" => "#0000FF"],
            ["nom" => "Bleu-vert", "code" => "#008080"],
            ["nom" => "Bordeaux", "code" => "#800000"],
            ["nom" => "Camel", "code" => "#C19A6B"],
            ["nom" => "Corail", "code" => "#FF7F50"],
            ["nom" => "Doré", "code" => "#FFD700"],
            ["nom" => "Fuchsia", "code" => "#FF00FF"],
            ["nom" => "Gris", "code" => "#808080"],
            ["nom" => "Jaune", "code" => "#FFFF00"],
            ["nom" => "Kaki", "code" => "#C3B091"],
            ["nom" => "Noir", "code" => "#000000"],
            ["nom" => "Nude", "code" => "#F5DEB3"],
            ["nom" => "Orange", "code" => "#FFA500"],
            ["nom" => "Rose", "code" => "#FFC0CB"],
            ["nom" => "Rouge", "code" => "#FF0000"],
            ["nom" => "Taupe", "code" => "#483C32"],
            ["nom" => "Turquoise", "code" => "#40E0D0"],
            ["nom" => "Vert", "code" => "#008000"],
            ["nom" => "Violet", "code" => "#800080"],
            ["nom" => "Multicolore", "code" => null],
        ];

        return collect($colors)->sortBy('nom')->values();
    }
}