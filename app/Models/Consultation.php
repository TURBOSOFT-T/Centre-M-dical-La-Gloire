<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code_consultation',
        'patient_id',
        'medecin_id',
        'date_heure_rdv',
        'type',
        'statut',
        'motif',
        'examen_physique',
        'diagnostic',
        'ordonnance',
        'notes_privees',
        'constantes',
        'tarif_brut',
        'est_paye',
    ];

    protected $casts = [
        'date_heure_rdv' => 'datetime',
        'constantes' => 'array',
        'est_paye' => 'boolean',
        'tarif_brut' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($consultation) {
            if (empty($consultation->code_consultation)) {
                $consultation->code_consultation = 'CNS-' . date('Y') . '-' . strtoupper(uniqid());
            }
        });
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}