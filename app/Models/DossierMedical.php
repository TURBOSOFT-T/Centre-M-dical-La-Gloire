<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
   public function modifiable()
    {
        if ($this->statut === 'actif' || $this->statut === 'archive') {
            return true;
        } else {
            return false;
        }
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
     * Les consultations associées au dossier médical.
     */


    /**
     * Les hospitalisations enregistrées dans ce dossier.
     */
    public function hospitalisations(): HasMany
    {
        return $this->hasMany(Hospitalisation::class);
    }

    /**
     * Les rendez-vous planifiés ou passés.
     */
  
   

    // ==========================================
    // METHODES UTILITAIRES & ACCESSEURS
    // ==========================================

    /**
     * Récupère l'hospitalisation active (en cours) si le patient est actuellement lités.
     */
    public function hospitalisationActive()
    {
        return $this->hospitalisations()->where('statut', 'en_cours')->latest()->first();
    }

    public function rendezVous()
{
    return $this->hasManyThrough(RendezVous::class, Patient::class, 'id', 'patient_id', 'patient_id', 'id');
}

    public function consultations()
{
    return $this->hasManyThrough(Consultation::class, Patient::class, 'id', 'patient_id', 'patient_id', 'id');
}
}