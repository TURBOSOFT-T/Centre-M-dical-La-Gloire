<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'consultations';

    protected $fillable = [
        'code_consultation',
        'patient_id',
        'medecin_id',
        'dossier_medical_id',
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
        'constantes'      => 'array',
        'est_paye'        => 'boolean',
        'tarif_brut'      => 'decimal:2',
    ];

      public function modifiable()
    {
        if ($this->statut === 'termine' || $this->statut === 'annule') {
            return false;
        } else {
            return true;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($consultation) {
            if (empty($consultation->code_consultation)) {
                $consultation->code_consultation = 'CNS-' . date('Y') . '-' . strtoupper(uniqid());
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé lisible du statut de la consultation.
     */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'en_attente'  => 'En attente',
            'en_cours'    => 'En cours',
            'terminee'    => 'Terminée',
            'annulee'     => 'Annulée',
            default       => ucfirst($this->statut ?? 'Non défini'),
        };
    }

    /**
     * Classes de badge Bootstrap en fonction du statut.
     */
    public function getStatutBadgeClassesAttribute(): string
    {
        return match ($this->statut) {
            'en_attente'  => 'bg-warning text-dark',
            'en_cours'    => 'bg-info text-white',
            'terminee'    => 'bg-success text-white',
            'annulee'     => 'bg-danger text-white',
            default       => 'bg-secondary text-white',
        };
    }

    /**
     * Helper pour récupérer une constante précise (ex: tension, température).
     */
    public function getConstante(string $key, $default = null)
    {
        return $this->constantes[$key] ?? $default;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Médecin traitant ayant réalisé la consultation.
     */
    public function medecin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    /**
     * Patient associé à la consultation.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Dossier médical rattaché.
     */
    public function dossierMedical(): BelongsTo
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }
}