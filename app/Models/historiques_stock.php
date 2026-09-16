<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historiques_stock extends Model
{
    use HasFactory;
    
    // Ajout des champs autorisés pour la sauvegarde (très utile si vous utilisez ::create())
    protected $fillable = [
        'quantite',
        'id_produit',
        'shop_id'
    ];

    /**
     * Relation vers le produit
     */
    public function produit() 
    {
        return $this->belongsTo(produits::class, 'id_produit');
    }

    /**
     * Relation vers la boutique (Shop)
     */
    public function shop() 
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}