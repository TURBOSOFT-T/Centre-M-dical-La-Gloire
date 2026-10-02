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
        Schema::table('hospitalisations', function (Blueprint $table) {
            // 1. Ajout du type de prise en charge (observation ou hospitalisation complète)
            $table->enum('type_prise_en_charge', ['observation', 'hospitalisation'])
                  ->default('hospitalisation')
                  ->after('agent_id');

            // 2. Ajout du type de standing / catégorie de chambre
            $table->enum('standing_type', [
                'haut_standing', 
                'classique', 
                'economique', 
                'post_up'
            ])->nullable()->after('type_prise_en_charge');

            // 3. Modification des champs pour les rendre optionnels (nécessaire pour l'observation)
            $table->string('chambre_number')->nullable()->change();
            $table->dateTime('date_entree')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            $table->dropColumn(['type_prise_en_charge', 'standing_type']);
            
            // Remise en état initial en cas de rollback (optionnel)
            $table->string('chambre_number')->nullable(false)->change();
            $table->dateTime('date_entree')->nullable(false)->change();
        });
    }
};