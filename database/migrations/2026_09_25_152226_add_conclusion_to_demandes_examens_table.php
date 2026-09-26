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
        Schema::table('demandes_examens', function (Blueprint $table) {
            // Ajout du champ conclusion pour les résultats d'analyses
            $table->text('conclusion')->nullable()->after('analyses_demandees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_examens', function (Blueprint $table) {
            $table->dropColumn('conclusion');
        });
    }
};