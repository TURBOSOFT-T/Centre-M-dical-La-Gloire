<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // Code / Identifiant Unique du Patient (ex: PAT-2026-0001)
            $table->string('code_patient')->unique();
            $table->unsignedBigInteger('assurance_id')->nullable();

            // Informations d'état civil
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->enum('genre', ['M', 'F']);
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('lieu_residence')->nullable();
            $table->string('profession')->nullable();
            $table->string('religion')->nullable();

            // Couverture Assurance (Liée à la table `assurances`)
            $table->boolean('est_assure')->default(false);
            $table->string('nom_assure')->nullable();
            //   $table->foreignId('assurance_id')->nullable()->constrained('assurances')->onDelete('set null');$table->string('nom_assure')->nullable(); // Si l'assuré principal est le conjoint/parent
            $table->string('matricule_assurance')->nullable(); // Numéro de carte/police
            $table->unsignedTinyInteger('taux_couverture')->nullable()->default(0); // Taux en % (ex: 80)

            // Constantes vitaux & Paramètres physiques (JSON)
            // Ex: {"poids": 75, "taille": 175, "temperature": 37.2, "tension": "12/8", "pouls": 72, "imc": 24.5, "spo2": 98}
            $table->json('parametres')->nullable();

            // Contact & Localisation
            $table->string('telephone')->unique();
            $table->string('telephone_whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->default('Douala');

            // Informations Médicales de base
            $table->enum('groupe_sanguin', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->text('allergies')->nullable();
            $table->text('antecedents_medicaux')->nullable();

            // Statut & Rattachement
            $table->boolean('est_actif')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes(); // Pour conserver l'historique médical
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
