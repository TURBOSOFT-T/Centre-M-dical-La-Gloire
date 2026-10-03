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
{Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn('traitement');
        });

        // 2. On recrée la colonne proprement au format JSON
        Schema::table('consultations', function (Blueprint $table) {
            $table->json('traitement')->nullable()->after('ordonnance');
        });
}

public function down(): void
{
   Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn('traitement');
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->text('traitement')->nullable();
        });
}
};
