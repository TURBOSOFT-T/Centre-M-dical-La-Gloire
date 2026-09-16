<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class produits extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [

        'nom',
        'description',
        'reference',
        'prix',
        'prix_achat',
        'photo',
        'id_promotion',
        'meta_description',

        'category_id',

        "sous_categorie_id",
        'famille_id',
        'id_shop',
        'stock',
        'statut',
        'photos',
        'free_shipping  ',

        'top',
        'active',
        'new',
        'points',
        'taille',
        'couleur',
        'is_new',
        'avec_commission'
    ];

    protected $casts = [
        'photos' => 'json',
        'taille' => 'array',
        'couleur' => 'array',
    ];

    protected $appends = [
        'final_price',
        'has_promotion',
        'total_sold',
        'slug',

    ];

    /**
     * Relation vers les stocks par boutique
     */
    public function stocks()
    {
        return $this->hasMany(ProductShop::class, 'produit_id');
    }
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'product_shop', 'produit_id', 'shop_id')
            ->withPivot('stock_particulier')

            ->withTimestamps();
    }


    public function promotion()
    {
        return $this->belongsTo(promotions::class, 'id_promotion');
    }
    public function vendus()
    {
        return $this->hasMany(contenu_commande::class, 'id_produit');
    }


    public function getPrice()
    {
        if ($this->promotion && $this->promotion->pourcentage) {
            return $this->prix - ($this->prix * $this->promotion->pourcentage / 100);
        }

        return $this->prix;
    }

    public function diminuerStockParTaille(int $tailleId, int $quantite): void
    {
        $taille = $this->tailles()->where('id', $tailleId)->first();

        if ($taille && $taille->pivot->stock >= $quantite) {
            // Met à jour le stock dans la table pivot
            $this->tailles()->updateExistingPivot($tailleId, [
                'stock' => $taille->pivot->stock - $quantite
            ]);
        } else {
            throw new \Exception("Stock insuffisant pour cette taille.");
        }
    }

    public function getFinalPriceAttribute()
    {
        return $this->getPrice();
    }

    public function getHasPromotionAttribute()
    {
        return $this->promotion !== null;
    }

    public function getTotalSoldAttribute()
    {
        return $this->vendus()->sum('quantite') ?? 0;
    }

    public function getTotalSoldAttribute1()
    {
        return $this->vendus()->count();
    }
    public function getPrice1()
    {
        if ($this->id_promotion) {
            $promotion = promotions::find($this->id_promotion);
            if ($promotion) {
                $price = $this->prix - ($this->prix * ($promotion->pourcentage / 100));
                return $price;
            } else {
                return $this->prix;
            }
        } else {
            return $this->prix;
        }
    }

    public function inPromotion()
    {
        if ($this->id_promotion) {
            $promotion = promotions::find($this->id_promotion);
            if ($promotion) {
                return $promotion;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /* public function diminuer_stock(int $quantite): void
    {
        if ($this->stock >= $quantite) {
            $this->stock -= $quantite;
            $this->save();
        }
    }

    public function retourner_stock(int $quantite): void
    {
        $this->stock += $quantite;
        $this->save();
    }
 */

    // Dans App\Models\produits.php

   
    public function diminuer_stock(int $quantite, ?int $shopId = null): void
    {
        $this->decrement('stock', $quantite);
        // 2. Diminuer le stock spécifique au magasin
        if ($shopId) {
            $updated = $this->shops()->updateExistingPivot($shopId, [
                'stock_particulier' => \DB::raw("stock_particulier - {$quantite}")
            ]);

            if (!$updated) {
                throw new \Exception("Impossible de mettre à jour le stock dans ce magasin.");
            }
        }
    }

    public function retourner_stock(int $quantite, ?int $shopId = null): void
    {
        if ($quantite <= 0) return;

        // 1. Mise à jour globale
        $this->increment('stock', $quantite); // Utilisation de decrement() pour plus de performance

        // 2. Mise à jour magasin
        if ($shopId) {
            $updated = $this->shops()->updateExistingPivot($shopId, [
                'stock_particulier' => \DB::raw("stock_particulier + {$quantite}")
            ]);

            if (!$updated) {
                throw new \Exception("Impossible de mettre à jour le stock dans ce magasin.");
            }
        }
    }

    public function historique_stock()
    {
        return $this->hasMany(historiques_stock::class, 'id_produit');
    }


    public function vues()
    {
        return $this->hasMany(views::class, 'id_produit');
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function marques()
    {
        return $this->belongsTo(Marque::class, 'marque_id', 'id');
    }


    public function getSlugAttribute()
    {
        return \Str::slug($this->nom . '-' . $this->id);
    }


    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    public function getReview()
    {
        return $this->hasMany('App\Models\Review', 'product_id', 'id');
    }

    public function getAverageReview()
    {
        return $this->hasMany('App\Models\Review', 'product_id', 'id')->avg('rating');
    }
    public function getReviewCount()
    {
        return $this->hasMany('App\Models\Review', 'product_id', 'id')->count();
    }


    public function sous_categories()
    {
        return $this->belongsTo(Sous_category::class, 'sous_categorie_id');
    }

    public function familles()
    {
        return $this->belongsTo(Famille::class, 'famille_id', 'id');
    }
}
