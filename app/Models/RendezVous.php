<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rendez_vous';

    protected $guarded = [];

    protected $casts = [
        'date_heure'     => 'datetime',
        'rappel_envoye'  => 'boolean',
        'tarif_brut'     => 'decimal:2',
        'part_assurance' => 'decimal:2',
        'part_patient'   => 'decimal:2',
        'montant_paye'   => 'decimal:2',
    ];


    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'planifie'   => 'Planifié',
            'confirme'   => 'Confirmé',
            'en_attente' => 'En attente',
            'honore'     => 'Honoré',
            'annule'     => 'Annulé',
            'absent'     => 'Absent',
            default      => ucfirst($this->statut),
        };
    }
    public function modifiable()
    {
        if ($this->statut === 'honore' || $this->statut === 'annule') {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Classes Tailwind pour le badge de statut.
     */
    public function getStatutBadgeClassesAttribute(): string
    {
        return match ($this->statut) {
            'planifie'   => 'bg-amber-100 text-amber-800 border-amber-200',
            'confirme'   => 'bg-blue-100 text-blue-800 border-blue-200',
            'en_attente' => 'bg-purple-100 text-purple-800 border-purple-200',
            'honore'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'annule'     => 'bg-rose-100 text-rose-800 border-rose-200',
            'absent'     => 'bg-slate-100 text-slate-800 border-slate-200',
            default      => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    // Relations
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

      public function dossierMedical(): BelongsTo
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }
}