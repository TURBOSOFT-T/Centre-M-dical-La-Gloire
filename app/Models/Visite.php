<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visite extends Model
{
    use HasFactory;

    protected $table = 'visites';

    protected $fillable = [
        'code_visite',
        'patient_id',
        'visiteur_id',
        'user_id',
        'date_heure_entree',
        'date_heure_sortie',
        'chambre_lit',
        'badge_numero',
        'observations'
    ];

    protected $casts = [
        'date_heure_entree' => 'datetime',
        'date_heure_sortie' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function visiteur()
    {
        return $this->belongsTo(Visiteur::class, 'visiteur_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessor pour savoir si le visiteur est actuellement dans l'établissement
    public function getEstEnCoursAttribute(): bool
    {
        return is_null($this->date_heure_sortie);
    }
}