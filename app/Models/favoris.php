<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favoris extends Model
{
    use HasFactory;

    //filiable
    protected $fillable = [
        'id_user',
        'id_produit',
    ];

    

    public function produit(){
        return $this->belongsTo(produits::class ,'id_produit');
    }

}
