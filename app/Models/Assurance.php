<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'nom',
        'telephone',
        'email',
        'adresse',
        'taux_couverture_defaut',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'taux_couverture_defaut' => 'integer',
    ];

    /**
     * Relation avec les patients souscrits à cette assurance
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}