<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class views extends Model
{
    use HasFactory;
     protected $fillable = [
        'id_produit',
        'ip_address'
        // Ajoutez vos autres colonnes ici si nécessaire
    ];

}
