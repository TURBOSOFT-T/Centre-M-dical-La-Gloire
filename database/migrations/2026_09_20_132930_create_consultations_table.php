<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('code_consultation')->unique(); // Ex: CNS-2026-0001

            // Rattachements (Placé juste après 'id')
            $table->foreignId('dossier_medical_id')
                ->nullable()
                ->constrained('dossiers_medicaux') // Assurez-vous du nom exact de la table
                ->onDelete('cascade');
                
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->nullable()->constrained('users')->onDelete('set null');

            // Rendez-vous & Timing
            $table->dateTime('date_heure_rdv'); // Modifié en dateTime si besoin d'inclure l'heure
            
            $table->enum('type', [
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
                'traumatologie', // Correction orthographique : traumatologie
                'ORL',
                'kinesithérapie',
                'nutrition',
                'autre'
            ])->default('medecin_general'); // Alignement avec une clé valide du tableau

            $table->enum('statut', ['programme', 'en_attente', 'en_cours', 'termine', 'annule'])->default('programme');

            // Médical
            $table->text('motif')->nullable();              // Motif de consultation / Symptômes
            $table->text('examen_physique')->nullable();     // Observation clinique
            $table->text('diagnostic')->nullable();          // Diagnostic médical
            $table->text('ordonnance')->nullable();          // Prescription / Médicaments
            $table->text('notes_privees')->nullable();       // Remarques confidentielles

            // Constantes au moment de la consultation (JSON)
            // Ex: {"poids":75, "tension":"12/8", "temperature":37.5, "pouls":78}
            $table->json('constantes')->nullable();

            // Facturation de la consultation
            $table->decimal('tarif_brut', 10, 2)->default(0);
            $table->boolean('est_paye')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};