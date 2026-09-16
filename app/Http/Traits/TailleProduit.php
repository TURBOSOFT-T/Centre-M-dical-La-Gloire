<?php

namespace App\Http\Traits;

trait TailleProduit
{

    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function getListTailleProduit()
    {
        // Les couleurs et leurs codes correspondants
        $tailles = [
          'XS',
          'S',
          'M',
          'L',
          'XL',
          'XXL',


        ];

        return collect($tailles);
    }
}