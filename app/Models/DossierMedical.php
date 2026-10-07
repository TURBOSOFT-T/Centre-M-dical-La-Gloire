<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DossierMedical extends Model
{
    use HasFactory;

    protected $table = 'dossiers_medicaux';

    protected $fillable = [
        'code_dossier',
        'patient_id',
        'groupe_sanguin',
        'antecedents_medicaux',
        'antecedents_chirurgicaux',
        'allergies',
        'traitements_chroniques',
        'statut',
    ];

    public function modifiable(): bool
    {
        return in_array($this->statut, ['actif', 'archive']);
    }

    protected static function booted()
    {
        static::deleting(function ($dossier) {
            // Suppression en cascade propre des éléments liés au dossier
            $dossier->hospitalisations()->delete();
            $dossier->consultations()->delete();
        });
    }

    // ==========================================
    // RELATIONS ELOQUENT
    // ==========================================

    /**
     * Le patient propriétaire de ce dossier médical.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Les hospitalisations associées au dossier.
     */
    public function hospitalisations(): HasMany
    {
        return $this->hasMany(Hospitalisation::class);
    }

    /**
     * Les consultations rattachées directement au dossier médical.
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'dossier_medical_id');
    }

    /**
     * Relation pratique pour obtenir directement la dernière consultation.
     */
    public function derniereConsultation(): HasOne
    {
        return $this->hasOne(Consultation::class, 'dossier_medical_id')->latestOfMany('created_at');
    }

    /**
     * Les rendez-vous du patient lié au dossier.
     */
    public function rendezVous()
    {
        return $this->hasManyThrough(RendezVous::class, Patient::class, 'id', 'patient_id', 'patient_id', 'id');
    }

    /**
     * Les examens du patient lié au dossier.
     */
    public function examens()
    {
        return $this->hasManyThrough(Examen::class, Patient::class, 'id', 'patient_id', 'patient_id', 'id');
    }

    /**
     * Demandes d'examens liées aux consultations du dossier.
     */
    public function demandesExamens()
    {
        return $this->hasManyThrough(
            DemandeExamen::class,
            Consultation::class,
            'dossier_medical_id', // Clé étrangère sur consultations
            'consultation_id',    // Clé étrangère sur demandes_examens
            'id',                 // Clé locale sur dossiers_medicaux
            'id'                  // Clé locale sur consultations
        );
    }

    // ==========================================
    // ACCESSEURS
    // ==========================================

    public function getDemandesExamensAttribute()
    {
        return $this->consultations->flatMap->demandesExamens;
    }
}