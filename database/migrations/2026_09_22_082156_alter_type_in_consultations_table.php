<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En MySQL / MariaDB : Mise à jour de la liste ENUM du champ 'type'
        DB::statement("ALTER TABLE consultations MODIFY COLUMN type ENUM(
            'consultation_generale',
            'medecin_general',
            'specialiste',
            'suivi',
            'urgence',
            'sage_femme',
            'pédiatrie',
            'cardiologie',
            'dermatologie',
            'gynécologie',
            'neurologie',
            'ophtalmologie',
            'orthopédie',
            'psychiatrie',
            'radiologie',
            'chirurgie',
            'dentisterie', 
            'tromatologie',
            'ORL',
            'kinesithérapie',
            'nutrition',
            'autre'
          
        ) NOT NULL DEFAULT 'consultation_generale'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE consultations MODIFY COLUMN type ENUM(
       'consultation_generale',
       'medecin_general',
            'specialiste',
            'suivi',
            'urgence',
            'sage_femme',
            'pédiatrie',
            'cardiologie',
            'dermatologie',
            'gynécologie',
            'neurologie',
            'ophtalmologie',
            'orthopédie',
            'psychiatrie',
            'radiologie',
            'chirurgie',
            'dentisterie', 
            'tromatologie',
            'ORL',
            'kinesithérapie',
            'nutrition',
            'autre'

        ) NOT NULL DEFAULT 'consultation_generale'");
    }
};