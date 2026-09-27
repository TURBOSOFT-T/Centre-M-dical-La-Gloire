<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->decimal('montant_paye', 10, 2)->default(0.00)->after('tarif_brut');
            // 'non_paye', 'partiel', 'paye'
            $table->enum('statut_paiement', ['non_paye', 'partiel', 'paye'])->default('non_paye')->after('est_paye');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['montant_paye', 'statut_paiement']);
        });
    }
};