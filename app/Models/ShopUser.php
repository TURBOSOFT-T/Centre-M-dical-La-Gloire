<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopUser extends Pivot
{
    /**
     * Nom de la table associée au modèle pivot.
     *
     * @var string
     */
    protected $table = 'shop_user';

    /**
     * Indique si les IDs sont auto-incrémentés.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Les attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'user_id',
        'is_active',
        'role_in_shop', // Optionnel si vous gérez des rôles spécifiques par boutique
    ];

    /* -------------------------------------------------------------------------- */
    /*                                 RELATIONS                                  */
    /* -------------------------------------------------------------------------- */

    /**
     * Obtenir la boutique associée.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Obtenir l'utilisateur (caissier ou commercial) associé.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}