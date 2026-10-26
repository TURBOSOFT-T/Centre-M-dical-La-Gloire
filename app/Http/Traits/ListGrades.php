<?php

namespace App\Http\Traits;

trait ListGrades
{

    public function  getListGrade()
    {
        return [
            "Mr",
            "Dr",
            "Mme",
        ];
    }
}
