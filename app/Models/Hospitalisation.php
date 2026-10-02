<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Hospitalisation extends Model
{
    use HasFactory;

    protected $table = 'hospitalisations';

    protected $fillable = [
        'code_hospitalisation',
        'dossier_medical_id',
        'patient_id',
        'medecin_id',
        'agent_id',
        'type_prise_en_charge', // 'observation' ou 'hospitalisation'
        'standing_type',        // 'haut_standing', 'classique', 'economique', 'post_op'
        'chambre_number',
        'lit_number',
        'service_department',
        'date_entree',
        'date_sortie_prevue',
        'date_sortie_effective',
        'nombre_jours',         // <-- Ajouté ici pour l'enregistrement en BDD
        'motif_admission',
        'diagnostic_entree',
        'diagnostic_sortie',
        'observations',
        'statut',
        'tarif_journalier',
        'frais_soins_chambre',
        'montant_total',
        'part_assurance',
        'part_patient',
        'montant_paye',
        'statut_paiement',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code_hospitalisation)) {
                $year = date('Y');
                $last = self::whereYear('created_at', $year)->latest('id')->first();
                $sequence = $last ? $last->id + 1 : 1;
                $model->code_hospitalisation = 'HOSP-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function modifiable()
    {
        if ($this->statut === 'libere' || $this->statut === 'decede' || $this->statut_paiement === 'paye') {
            return false;
        }
        return true;
    }

    /**
     * Cast automatique des attributs
     */
    protected $casts = [
        'date_entree' => 'datetime',
        'date_sortie_prevue' => 'datetime',
        'date_sortie_effective' => 'datetime',
        'nombre_jours' => 'integer', // <-- Cast en entier
        'tarif_journalier' => 'decimal:2',
        'frais_soins_chambre' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'part_assurance' => 'decimal:2',
        'part_patient' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONS ELOQUENT
    // ==========================================

    public function dossierMedical(): BelongsTo
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // ==========================================
    // ACCESSEURS & LOGIQUE MÉTIER
    // ==========================================

    /**
     * Calcule automatiquement le montant total selon le type de prise en charge.
     */
    public function calculerMontantTotal(): float
    {
        if ($this->type_prise_en_charge === 'observation') {
            return (float) ($this->tarif_journalier + $this->frais_soins_chambre);
        }

        // Utilise le nombre de jours enregistré en base, ou 1 par défaut
        $jours = $this->nombre_jours ?? 1;
        $fraisSejour = $jours * $this->tarif_journalier;
        
        return (float) ($fraisSejour + $this->frais_soins_chambre);
    }

    /**
     * Vérifie si la prise en charge est toujours en cours.
     */
    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }
}