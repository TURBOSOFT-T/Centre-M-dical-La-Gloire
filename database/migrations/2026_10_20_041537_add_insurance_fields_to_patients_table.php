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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('societe')->nullable()->after('assurance_id');
            $table->string('matricule_assure')->nullable()->after('societe');
            $table->string('matricule_assure_principal')->nullable()->after('matricule_assure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'societe',
                'matricule_assure',
                'matricule_assure_principal',
            ]);
        });
    }
};