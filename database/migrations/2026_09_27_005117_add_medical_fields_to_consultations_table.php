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
        Schema::table('consultations', function (Blueprint $table) {
            // Anamnèse & Historique
            $table->text('historique_maladie')->nullable()->after('motif');
            $table->text('antecedents_maladie')->nullable()->after('historique_maladie');
            $table->text('mode_de_vie')->nullable()->after('antecedents_maladie');

            // Examens & Diagnostics
            $table->text('examen_general')->nullable()->after('examen_physique');
            $table->text('hypothese_diagnostique')->nullable()->after('examen_general');
            $table->text('resultats_analyses')->nullable()->after('hypothese_diagnostique');

            // Évaluations dynamiques (JSON)
            $table->json('evaluations')->nullable()->after('diagnostic');

            // Traitements
            $table->text('traitement')->nullable()->after('ordonnance');
            $table->text('traitement_sortie')->nullable()->after('traitement');

            // Visite médicale journalière / Suivi (JSON)
            $table->json('visite_medicale_journaliere')->nullable()->after('constantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'historique_maladie',
                'antecedents_maladie',
                'mode_de_vie',
                'examen_general',
                'hypothese_diagnostique',
                'resultats_analyses',
                'evaluations',
                'traitement',
                'traitement_sortie',
                'visite_medicale_journaliere',
            ]);
        });
    }
};