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
        $table->enum('prise_en_charge', ['mise_en_observation', 'hospitalisation'])->nullable()->after('statut');
    });
}

public function down(): void
{
    Schema::table('consultations', function (Blueprint $table) {
        $table->dropColumn('prise_en_charge');
    });
}
};
