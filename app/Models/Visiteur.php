<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visiteur extends Model
{
    use HasFactory;

    protected $table = 'visiteurs';

    protected $fillable = [
        'nom_complet',
        'telephone',
        'cni_ou_piece',
        'lien_parente'
    ];

    public function visites()
    {
        return $this->hasMany(Visite::class, 'visiteur_id');
    }
}