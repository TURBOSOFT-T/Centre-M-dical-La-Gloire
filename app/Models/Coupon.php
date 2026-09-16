<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

   protected $fillable = [
    'code',
    'type',
    'value',
    'status',
    'is_commercial', // <-- Ajouté ici
    'expires_at',
    'user_id',
    'commercial_id',
];

protected $casts = [
    'expires_at' => 'date',
    'value' => 'float',
    'is_commercial' => 'boolean', // <-- Ajouté ici pour le casting automatique
];
    public static function findByCode($code){
        return self::where('code',$code)->first();
    }
  
    public function users()
    {
        return $this->belongsTo(User::class, 'commercial_id')->withDefault();
    }

    // Coupon.php
public function commercial()
{
    return $this->belongsTo(User::class, 'commercial_id');
}


  /*   public function isValid()
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    } */
}
