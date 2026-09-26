<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeExamen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'demandes_examens';

    protected $guarded = [];
    protected $fillable = [
        'effectue_par',
        'consultation_id',
        'examen_id',
        'patient_id',
        'dossier_medical_id',

        'prescrit_par',
        'indication_medicale',
        'analyses_demandees',
        'statut',
        'tarif_brut',
        'part_assurance',
        'part_patient',
        'est_paye',

    ];
    protected $casts = [
        'analyses_demandees' => 'array',
        'date_prescrite'     => 'datetime',
        'date_realisation'   => 'datetime',
        'est_paye'           => 'boolean',
        'tarif_brut'         => 'decimal:2',
        'part_assurance'     => 'decimal:2',
        'part_patient'       => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($demande) {
            if (empty($demande->code_demande)) {
                $demande->code_demande = 'EXM-' . date('Y') . '-' . strtoupper(uniqid());
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & HELPERS
    |--------------------------------------------------------------------------
    */

    public function getStatutBadgeAttribute(): string
    {
        return match ($this->statut) {
            'prescrit'            => 'bg-info text-white',
            'en_attente_paiement' => 'bg-warning text-dark',
            'en_cours'            => 'bg-primary text-white',
            'termine'             => 'bg-success text-white',
            'annule'              => 'bg-danger text-white',
            default               => 'bg-secondary text-white',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function dossierMedical(): BelongsTo
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function prescripteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prescrit_par');
    }

    public function biologiste(): BelongsTo
    {
        return $this->belongsTo(User::class, 'effectue_par');
    }

    /**
     * Alias de la relation pour correspondre au contrôleur des résultats d'examens
     */
    public function realisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realise_par');
    }

   

public function validerPaiementExamen($demandeId)
{
    $demande = DemandeExamen::findOrFail($demandeId);

    // 1. Enregistrement du paiement / transaction caisse
    $demande->update([
        'est_paye' => true,
        // Optionnel : enregistrer la date et l'agent qui a encaissé
        'paye_le' => now(),
        'paye_par' => auth()->id(),
    ]);

    session()->flash('message', 'Paiement enregistré avec succès pour la demande N° ' . $demande->code_demande);
}
}
