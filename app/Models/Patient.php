<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code_patient',
        'nom',
        'prenom',
        'genre',
        'date_naissance',
        'lieu_naissance',
        'lieu_residence',
        'profession',
        'religion',
        'est_assure',
        'assurance_id',
        'nom_assure',
        'matricule_assurance',
        'taux_couverture',
        'parametres',
        'telephone',
        'telephone_whatsapp',
        'email',
        'adresse',
        'ville',
        'groupe_sanguin',
        'allergies',
        'antecedents_medicaux',
        'est_actif',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'parametres' => 'array',
        'est_assure' => 'boolean',
        'est_actif' => 'boolean',
        'date_naissance' => 'date',
        'taux_couverture' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->code_patient)) {
                $patient->code_patient = 'CMG-' . date('Y') . '-' . strtoupper(uniqid());
            }
        });
    }

    public function getNomCompletAttribute()
    {
        return trim("{$this->nom} {$this->prenom}");
    }

    /**
     * Relation avec l'organisme d'assurance
     */
    public function assurance()
    {
        return $this->belongsTo(Assurance::class, 'assurance_id');
    }

    public function visites()
{
    return $this->hasMany(Visite::class, 'patient_id');
}/**
     * Relation avec le dossier médical du patient
     */
    public function dossierMedical()
    {
        return $this->hasOne(DossierMedical::class, 'patient_id');
    }

    /**
     * Relation avec les rendez-vous du patient
     */
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'patient_id')->orderBy('date_heure', 'desc');
    }

       public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }


}