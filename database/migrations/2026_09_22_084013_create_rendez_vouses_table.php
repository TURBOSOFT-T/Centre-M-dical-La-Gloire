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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->string('code_rdv')->unique(); // Ex: RDV-2026-0001

            // Rattachements
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cree_par')->nullable()->constrained('users')->onDelete('set null'); // Agent d'accueil/secrétaire
 $table->foreignId('dossier_medical_id')->nullable()->constrained('dossiers_medicaux')->nullOnDelete();

            // Timing
            $table->dateTime('date_heure'); // Date et heure du RDV
            $table->integer('duree_minutes')->default(30); // Durée estimée en minutes

            // Classification
            $table->string('type')->default('consultation_generale'); // consultation_generale, specialiste, controle, bilan, suivi...

            // Frais & Paiement du Rendez-vous
            $table->decimal('tarif_brut', 10, 2)->default(0);      // Montant total du RDV / consultation
            $table->decimal('part_assurance', 10, 2)->default(0);  // Prise en charge mutuelle / assurance
            $table->decimal('part_patient', 10, 2)->default(0);    // Reste à payer par le patient
            $table->decimal('montant_paye', 10, 2)->default(0);    // Somme effectivement versée à la réservation
            
            $table->enum('statut_paiement', [
                'non_paye',
                'partiel',
                'paye',
                'rembourse'
            ])->default('non_paye');

            $table->enum('mode_paiement', [
                'especes',
                'mobile_money',
                'carte_bancaire',
                'assurance',
                'autre'
            ])->nullable();

            $table->string('reference_transaction')->nullable(); // Numéro reçu ou réference Mobile Money/CB

            // Statut du Rendez-vous
            $table->enum('statut', [
                'planifie',    // Programmé
                'confirme',    // Confirmé par le patient
                'en_attente',  // Le patient est arrivé en salle d'attente
                'honore',      // Le RDV a eu lieu (converti en consultation)
                'annule',      // Annulé à l'avance
                'absent'       // Le patient ne s'est pas présenté (No-show)
            ])->default('planifie');

            // Détails & Remarques
            $table->text('motif')->nullable(); // Raison de la visite ou symptômes
            $table->text('motif_annulation')->nullable(); // Explication si annulé
            $table->boolean('rappel_envoye')->default(false); // SMS/WhatsApp/Email de rappel envoyé

            // Lien direct vers la consultation générée (si le RDV est honoré)
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};