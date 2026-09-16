<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class templates extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta_error'
    ];
    
    public function produit(){
        return $this->belongsTo(produits::class, 'id_produit');
    }

    public function domaine(){
        return $this->belongsTo(domaines::class, 'id_domaine');
    }


   
}
