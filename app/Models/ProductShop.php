<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShop extends Model
{
    use HasFactory;

    // Force Laravel à utiliser le bon nom de table au singulier
    protected $table = 'product_shop';

    // Autorise le remplissage de vos colonnes de gestion de stock
    protected $fillable = [
        'produit_id',
        'shop_id',
        'stock_particulier',
        'price_particulier', // Si vous avez ajouté ce champ précédemment
        'stock_alerte'       // Si vous avez ajouté ce champ précédemment
    ];

    /**
     * Relation vers le produit
     */
    public function produit()
    {
        return $this->belongsTo(produits::class, 'produit_id');
    }

    /**
     * Relation vers la boutique
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}