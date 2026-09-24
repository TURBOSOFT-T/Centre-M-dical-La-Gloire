<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
        'chambre_number',
        'lit_number',
        'service_department',
        'date_entree',
        'date_sortie_prevue',
        'date_sortie_effective',
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


       public function modifiable()
    {
        if ($this->statut === 'retournée' || $this->statut === 'payée' || $this->statut === 'livrée') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Cast automatique des dates en instances Carbon
     */
    protected $casts = [
        'date_entree' => 'datetime',
        'date_sortie_prevue' => 'datetime',
        'date_sortie_effective' => 'datetime',
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

    /**
     * Dossier médical auquel appartient cette hospitalisation.
     */
    public function dossierMedical(): BelongsTo
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    /**
     * Patient hospitalisé.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Médecin responsable du suivi durant l'hospitalisation.
     */
    public function medecin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    /**
     * Agent/Utilisateur ayant saisi l'admission.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // ==========================================
    // ACCESSEURS & LOGIQUE MÉTIER
    // ==========================================

    /**
     * Calcule le nombre de jours/nuits passés à l'hôpital.
     */
    public function getNombreJoursAttribute(): int
    {
        $dateFin = $this->date_sortie_effective ?? now();
        $nbrJours = $this->date_entree->diffInDays($dateFin);

        // Au moins 1 jour si l'admission à eu lieu aujourd'hui
        return max(1, (int) $nbrJours);
    }

    /**
     * Calcule automatiquement le montant total des frais de séjour.
     */
    public function calculerMontantTotal(): float
    {
        $fraisSejour = $this->nombre_jours * $this->tarif_journalier;
        return $fraisSejour + $this->frais_soins_chambre;
    }

    /**
     * Vérifie si l'hospitalisation est toujours en cours.
     */
    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }
}