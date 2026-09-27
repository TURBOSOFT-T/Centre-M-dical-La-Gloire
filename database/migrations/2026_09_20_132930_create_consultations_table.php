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

            // Foreign Keys
            $table->foreignId('dossier_medical_id')
                ->nullable()
                ->constrained('dossiers_medicaux')
                ->onDelete('cascade');
                
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->onDelete('cascade');

            $table->foreignId('medecin_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Planning & Statuts
            $table->dateTime('date_heure_rdv')->index();
            
            $table->enum('type', [
                'consultation_generale',
                'medecin_general',
                'specialiste',
                'suivi',
                'urgence',
                'sage_femme',
                'pediatrie',
                'cardiologie',
                'dermatologie',
                'gynecologie',
                'neurologie',
                'ophtalmologie',
                'orthopedie',
                'psychiatrie',
                'radiologie',
                'chirurgie',
                'dentisterie', 
                'traumatologie',
                'orl',
                'kinesitherapie',
                'nutrition',
                'autre'
            ])->default('specialiste');

            $table->enum('statut', [
                'programme', 
                'en_attente', 
                'en_cours', 
                'termine', 
                'annule'
            ])->default('programme')->index();

            // Données Médicales
            $table->text('motif')->nullable();
            $table->text('historique_maladie')->nullable();
            $table->text('antecedents_maladie')->nullable();
            $table->text('mode_de_vie')->nullable();

            // Examens & Clinique
            $table->text('examen_physique')->nullable();
            $table->text('examen_general')->nullable();
            $table->text('hypothese_diagnostique')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('resultats_analyses')->nullable();
            $table->text('ordonnance')->nullable();
            $table->text('notes_privees')->nullable();

            // Traitements
            $table->text('traitement')->nullable();
            $table->text('traitement_sortie')->nullable();

            // Données Structurées (JSON)
            $table->json('constantes')->nullable(); // {"poids": 75, "ta": "12/8", "temp": 37.5}
            $table->json('evaluations')->nullable();
            $table->json('visite_medicale_journaliere')->nullable();

            // Facturation
            $table->decimal('tarif_brut', 10, 2)->default(0.00);
            $table->boolean('est_paye')->default(false)->index();
             $table->decimal('montant_paye', 10, 2)->default(0.00);

           
            $table->text('historique_paiements')->nullable();
            // 'non_paye', 'partiel', 'paye'
            $table->enum('statut_paiement', ['non_paye', 'partiel', 'paye'])->default('non_paye');
    

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};