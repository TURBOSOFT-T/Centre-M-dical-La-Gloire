<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historiques_connexion extends Model
{
    use HasFactory;

       protected $fillable = [
        'quantite',
        'id_produit',
        'shop_id'
    ];
}
