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
             $table->unsignedBigInteger('user_id')->nullable();
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
// ==========================================
            // CHAMPS SPÉCIFIQUES - FEMMES ENCEINTES / CPN
            // ==========================================
            $table->boolean('is_pregnant')->default(false); // Indique si la patiente est enceinte
            $table->integer('gestational_age_weeks')->nullable(); // Âge de la grossesse en Semaines d'Aménorrhée (SA)
            $table->date('expected_delivery_date')->nullable(); // Date Probable d'Accouchement (DPA)
            $table->integer('gravida')->nullable(); // Nombre total de grossesses (G)
            $table->integer('parity')->nullable(); // Nombre d'accouchements / parité (P)
            $table->integer('prenatal_visit_number')->nullable(); // Numéro de la CPN (ex: 1 pour CPN1, 2 pour CPN2...)
            $table->string('fetal_heart_rate')->nullable(); // Bruits du Cœur Fœtal (BCF)
            $table->text('pregnancy_notes')->nullable(); // Observations obstétricales particulières
            // Examens & Clinique
            $table->text('examen_physique')->nullable();
            $table->text('examen_general')->nullable();
            $table->text('hypothese_diagnostique')->nullable();
            $table->text('diagnostic')->nullable();
             $table->text('complication')->nullable();
            $table->text('resultats_analyses')->nullable();
            $table->text('ordonnance')->nullable();
            $table->text('notes_privees')->nullable();
            $table->json('bilan')->nullable(); // Stockage des examens à cocher
            $table->text('terrain')->nullable(); // Particularités / Allergies / Terrain
            $table->text('resultats')->nullable(); // Résultats tex
            
              $table->json('modifications_historique')->nullable();
              $table->text('evolution_maladie')->nullable();
              

            $table->boolean('est_modifie')->default(false); // Indique si la fiche a été modifiée
            $table->boolean('vu_par_responsable')->default(false); // Validé par le responsable
            $table->dateTime('date_vu_responsable')->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null')->after('date_vu_responsable');
            // Traitements
            $table->json('traitement')->nullable();
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
             $table->enum('prise_en_charge', ['mise_en_observation', 'hospitalisation'])->nullable();



            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
