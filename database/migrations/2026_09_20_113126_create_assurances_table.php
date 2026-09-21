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
        // 1. Table des organismes d'assurance / mutuelles
        Schema::create('assurances', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ex: CNPS, ASC-01, SAH-DLA
            $table->string('nom'); // Ex: CNPS, ASCOMA, SAHAM ASSURANCES, AXA SANTE
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            
            // Taux de couverture par défaut (ex: 80 pour 80%)
            $table->unsignedTinyInteger('taux_couverture_defaut')->default(80);
            
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Mise à jour de la table patients pour se lier à la table assurances
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
        Schema::dropIfExists('assurances');
    }
};