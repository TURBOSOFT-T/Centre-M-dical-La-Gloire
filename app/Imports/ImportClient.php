<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportClient implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
        $filteredData = [];

        // Filtrer et stocker uniquement les colonnes nécessaires
        foreach ($array as $row) {
            $filteredData[] = [
                'full_name' => $row['full_name'],
                'phone_number' => $row['phone_number']
            ];
        }

        return $filteredData;
    }
}
