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
        // Table des identités des visiteurs
        Schema::create('visiteurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('telephone');
            $table->string('cni_ou_piece')->nullable(); // N° CNI, Passeport ou Pièce d'identité pour la sécurité
            $table->string('lien_parente')->nullable(); // Ex: Frère, Époux(se), Parent, Enfant, Ami(e)
            $table->timestamps();
        });

        // Table du registre des visites effectuées
        Schema::create('visites', function (Blueprint $table) {
            $table->id();
            $table->string('code_visite')->unique(); // Ex: VIS-2026-001
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('visiteur_id')->constrained('visiteurs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Agent d'accueil / Secrétaire

            $table->dateTime('date_heure_entree');
            $table->dateTime('date_heure_sortie')->nullable();
            $table->string('chambre_lit')->nullable(); // Ex: Chambre 104 - Lit B
            $table->integer('badge_numero')->nullable(); // N° du badge visiteur remis à l'accueil
            $table->text('observations')->nullable(); // Ex: Dépôt d'effets personnels, nourriture, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visites');
        Schema::dropIfExists('visiteurs');
    }
};