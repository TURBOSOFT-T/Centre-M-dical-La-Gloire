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
        'nombre_jours',         
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
        'visite_urgentiste_qte',
        'visite_urgentiste_pu',
        'visite_urgentiste_total',
        'soin_ami_qte',
        'soin_ami_pu',
        'soin_ami_total',
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
        'nombre_jours' => 'integer',
        'tarif_journalier' => 'decimal:2',
        'frais_soins_chambre' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'part_assurance' => 'decimal:2',
        'part_patient' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'visite_urgentiste_qte' => 'integer',
        'visite_urgentiste_pu' => 'decimal:2',
        'visite_urgentiste_total' => 'decimal:2',
        'soin_ami_qte' => 'integer',
        'soin_ami_pu' => 'decimal:2',
        'soin_ami_total' => 'decimal:2',
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
     * Calcule automatiquement le montant total selon le type de prise en charge et les prestations.
     */
    public function calculerMontantTotal(): float
    {
        // 1. Calcul des frais de séjour
        if ($this->type_prise_en_charge === 'observation') {
            $fraisSejour = floatval($this->tarif_journalier ?? 0);
        } else {
            $jours = $this->nombre_jours ?? 1;
            $fraisSejour = $jours * floatval($this->tarif_journalier ?? 0);
        }

        // 2. Calculs dynamiques sous-totaux
        $totalVisiteUrgentiste = intval($this->visite_urgentiste_qte ?? 0) * floatval($this->visite_urgentiste_pu ?? 0);
        $totalSoinAmi = intval($this->soin_ami_qte ?? 0) * floatval($this->soin_ami_pu ?? 0);

        // Mise à jour directe des propriétés de sous-totaux
        $this->visite_urgentiste_total = $totalVisiteUrgentiste;
        $this->soin_ami_total = $totalSoinAmi;

        // 3. Somme globale de l'hospitalisation
        return (float) (
            $fraisSejour 
            + floatval($this->frais_soins_chambre ?? 0) 
            + $totalVisiteUrgentiste 
            + $totalSoinAmi
        );
    }

    /**
     * Vérifie si la prise en charge est toujours en cours.
     */
    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }
}