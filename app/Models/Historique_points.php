<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historique_points extends Model
{
    use HasFactory;
   protected $fillable = [
    
        'user_id',
        'commande_id',
        'montant',
        'produit_id'
    
        ];
    
    public function  produit() {
        return $this->belongsTo(produits::class , 'produit_id');
    }

    public function  commande() {
        return $this->belongsTo(commandes::class , 'commande_id');
    }
}
