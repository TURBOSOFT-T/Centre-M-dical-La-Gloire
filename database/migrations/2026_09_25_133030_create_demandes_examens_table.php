<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_examens', function (Blueprint $table) {
            $table->id();
            $table->string('code_demande')->unique(); // Ex: EXM-2026-0001

            // Rattachements
            $table->foreignId('consultation_id')
                ->constrained('consultations')
                ->onDelete('cascade');

            $table->foreignId('examen_id')
                ->constrained('examens')
                ->onDelete('cascade'); // Examen du catalogue

            $table->foreignId('patient_id')
                ->constrained('patients')
                ->onDelete('cascade');

            $table->foreignId('dossier_medical_id')
                ->nullable()
                ->constrained('dossiers_medicaux')
                ->onDelete('cascade');

            $table->foreignId('prescrit_par')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Médecin prescripteur

            $table->foreignId('effectue_par')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Laborantin / Biologiste

            // Analyses sélectionnées & Résultats enregistrés (JSON)
            // Ex: [{"nom": "Hémoglobine", "prix": 2500, "resultat": "13.5 g/dL", "norme": "12-16"}]
            $table->json('analyses_demandees')->nullable();
            
            $table->text('indication_medicale')->nullable(); // Ex: Suspicion d'anémie
            $table->text('conclusion_biologiste')->nullable(); // Remarques du labo

            // Statuts & Dates
            $table->enum('statut', ['prescrit', 'en_attente_paiement', 'en_cours', 'termine', 'annule'])->default('prescrit');
            $table->dateTime('date_prescrite')->useCurrent();
            $table->dateTime('date_realisation')->nullable();

            // Volet financier calculé automatiquement selon les analyses cochées
            $table->decimal('tarif_brut', 10, 2)->default(0);
            $table->decimal('part_assurance', 10, 2)->default(0);
            $table->decimal('part_patient', 10, 2)->default(0);
            $table->boolean('est_paye')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_examens');
    }
};