<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->json('bilan')->nullable()->after('ordonnance'); // Stockage des examens à cocher
            $table->text('terrain')->nullable()->after('antecedents_maladie'); // Particularités / Allergies / Terrain
            $table->text('resultats')->nullable()->after('resultats_analyses'); // Résultats textuels détaillés
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['bilan', 'terrain', 'resultats']);
        });
    }
};