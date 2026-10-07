<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
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
        'is_synced',
    ];

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function getRouteKeyName()
    {
        return 'uuid'; // Utile si vous voulez utiliser l'UUID dans vos URLs à la place de l'ID
    }

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

    protected static function booted()
    {
        static::deleting(function ($patient) {
            // Supprime toutes les consultations associées lors de la suppression du patient
            $patient->consultations()->delete();
        });

        static::deleting(function ($patient) {
            // Supprime toutes les consultations associées lors de la suppression du patient
            $patient->visites()->delete();
            $patient->dossierMedical()->delete();
            $patient->commandes()->delete();
            $patient->rendezVous()->delete();
        });
    }
public function getAgeAttribute(): ?int
{
    return $this->date_naissance ? \Carbon\Carbon::parse($this->date_naissance)->age : null;
}
 
public function getNomCompletAttribute(): string
{
    return "{$this->nom} {$this->prenom}";
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
    }
    /**
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

    public function commandes()
    {
        return $this->hasMany(commandes::class, "phone");
    }
}
