<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'tarif_brut',
        'montant_paye',
        'historique_paiements',
        'est_paye',

        // Anamnèse & Motif
        'motif',
        'historique_maladie',
        'antecedents_maladie',
        'mode_de_vie',

        // Examens Cliniques & Diagnostics
        'examen_physique',
        'examen_general',
        'hypothese_diagnostique',
        'diagnostic',
        'resultats_analyses',

        // Traitements & Prescriptions
        'ordonnance',
        'traitement',
        'traitement_sortie',
        'notes_privees',

        // Données JSON / Structurées
        'constantes',
        'evaluations',
        'visite_medicale_journaliere',
        'bilan',
        'terrain',
        'resultats',
        'est_modifie',
        'vu_par_responsable',
        'date_vu_responsable',
        'responsable_id',
        'modifications_historique',
        'evolution_maladie'
    ];

    protected $casts = [
        'date_heure_rdv'              => 'datetime',
        'tarif_brut'                  => 'decimal:2',
        'montant_paye'                => 'decimal:2',
        'est_paye'                    => 'boolean',
        'constantes'                  => 'array',
        'evaluations'                 => 'array',
        'visite_medicale_journaliere' => 'array',
        'historique_paiements'        => 'array',
        'bilan' => 'array',
        'modifications_historique' => 'array',
        // 'modifications_historique' => 'array'
    ];
    /**
     * Calcul du montant total cumulé (Consultation + Examens prescrits)
     */

    public function getResteAPayerAttribute(): float
    {
        $total = $this->tarif_brut + ($this->demandesExamens ? $this->demandesExamens->sum('tarif_brut') : 0);
        return max(0, $total - $this->montant_paye);
    }
    public function getTotalFactureAttribute(): float
    {
        $tarifConsultation = (float) ($this->tarif_brut ?? 0);
        $tarifExamens = $this->demandesExamens ? (float) $this->demandesExamens->sum('tarif_brut') : 0;

        return $tarifConsultation + $tarifExamens;
    }

    /**
     * Calcul du reste à payer par le patient
     */
    public function getResteAPayerAttribute2(): float
    {
        $total = $this->total_facture;

        // Si le patient a une assurance, calcul du reste à charge
        if ($this->patient && $this->patient->est_assure && $this->patient->assurance) {
            $taux = (float) $this->patient->taux_couverture;
            $partAssurance = round(($total * $taux) / 100);
            $total = $total - $partAssurance;
        }

        $reste = $total - (float) ($this->montant_paye ?? 0);

        return max($reste, 0);
    }

    /**
     * Vérifie si la consultation est modifiable.
     */
    public function modifiable(): bool
    {
        return !in_array($this->statut, ['termine', 'annule']);
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
            'programme'  => 'Programmé',
            'en_attente' => 'En attente',
            'en_cours'   => 'En cours',
            'termine'    => 'Terminée',
            'annule'     => 'Annulée',
            default      => ucfirst($this->statut ?? 'Non défini'),
        };
    }

    /**
     * Classes de badge Bootstrap en fonction du statut.
     */
    public function getStatutBadgeClassesAttribute(): string
    {
        return match ($this->statut) {
            'programme'  => 'bg-primary text-white',
            'en_attente' => 'bg-warning text-dark',
            'en_cours'   => 'bg-info text-white',
            'termine'    => 'bg-success text-white',
            'annule'     => 'bg-danger text-white',
            default      => 'bg-secondary text-white',
        };
    }

    /**
     * Helper pour récupérer une constante précise (ex: tension, température).
     */
    public function getConstante(string $key, $default = null)
    {
        return $this->constantes[$key] ?? $default;
    }

    /**
     * Helper pour récupérer une valeur d'évaluation clinique.
     */
    public function getEvaluation(string $key, $default = null)
    {
        return $this->evaluations[$key] ?? $default;
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

    /**
     * Demandes d'examens biologiques ou d'imagerie associées.
     */
    public function demandesExamens(): HasMany
    {
        return $this->hasMany(DemandeExamen::class, 'consultation_id');
    }
}
