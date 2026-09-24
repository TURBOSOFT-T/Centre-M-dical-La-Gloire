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
        Schema::create('hospitalisations', function (Blueprint $table) {
            $table->id();
            
            // Identification & Liaisons
            $table->string('code_hospitalisation')->unique(); // Ex: HOSP-2026-0001
            $table->foreignId('dossier_medical_id')->constrained('dossiers_medicaux')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->nullable()->constrained('users')->nullOnDelete(); // Médecin traitant / responsable
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete(); // Agent qui enregistre

            // Emplacement / Hébergement
            $table->string('chambre_number'); // N° ou nom de la chambre
            $table->string('lit_number')->nullable();  // N° du lit
            $table->string('service_department')->nullable(); // Ex: Cardiologie, Pédiatrie, Chirurgie, Maternité

            // Dates & Suivi Temporel
            $table->dateTime('date_entree');
            $table->dateTime('date_sortie_prevue')->nullable();
            $table->dateTime('date_sortie_effective')->nullable();

            // Motif & Diagnostic
            $table->text('motif_admission');
            $table->text('diagnostic_entree')->nullable();
            $table->text('diagnostic_sortie')->nullable();
            $table->text('observations')->nullable();

            // Statut de l'hospitalisation
            $table->enum('statut', [
                'en_cours',      // Patient actuellement au lit
                'libere',        // Sortie autorisée et effectuée
                'transfere',     // Transféré vers un autre centre ou service
                'decede',        // Décès pendant l'hospitalisation
                'annule'         // Hospitalisation annulée
            ])->default('en_cours');

            // Tarification & Frais de séjour
            $table->decimal('tarif_journalier', 12, 2)->default(0); // Coût du lit / nuitée
            $table->decimal('frais_soins_chambre', 12, 2)->default(0); // Cumul soins/gardes
            $table->decimal('montant_total', 12, 2)->default(0);      // Montant total calculé
            $table->decimal('part_assurance', 12, 2)->default(0);     // Prise en charge
            $table->decimal('part_patient', 12, 2)->default(0);       // Reste à charge
            $table->decimal('montant_paye', 12, 2)->default(0);        // Avances/Règlements reçus

            $table->enum('statut_paiement', [
                'non_paye',
                'partiel',
                'paye',
                'rembourse'
            ])->default('non_paye');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitalisations');
    }
};