<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clients extends Model
{
    use HasFactory;
  protected $fillable = [
        'nom',
        'prenom',
        'email',
        'adresse',
        'gouvernorat',
        'avatar',
        'password',
        'phone',
         'solde',
        'points'
    ];
  

    public function commandes(){
        return $this->hasMany(commandes::class,"phone");
    }


}
