<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class config extends Model
{
    use HasFactory;
    protected $table = 'configs';

    protected $fillable = [
        'logo',

        'telephone',

        'addresse',
        'email',
        'description',

        'icon',






    ];
}
