<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->boolean('est_modifie')->default(false)->after('statut'); // Indique si la fiche a été modifiée
            $table->boolean('vu_par_responsable')->default(false)->after('est_modifie'); // Validé par le responsable
            $table->dateTime('date_vu_responsable')->nullable()->after('vu_par_responsable');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null')->after('date_vu_responsable');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn(['est_modifie', 'vu_par_responsable', 'date_vu_responsable', 'responsable_id']);
        });
    }
};