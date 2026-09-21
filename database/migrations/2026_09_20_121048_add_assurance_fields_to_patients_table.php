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
            if (!Schema::hasColumn('patients', 'est_assure')) {
                $table->boolean('est_assure')->default(false)->after('religion');
            }
            if (!Schema::hasColumn('patients', 'assurance_id')) {
                $table->foreignId('assurance_id')->nullable()->after('est_assure')->constrained('assurances')->onDelete('set null');
            }
            if (!Schema::hasColumn('patients', 'nom_assure')) {
                $table->string('nom_assure')->nullable()->after('assurance_id');
            }
            if (!Schema::hasColumn('patients', 'matricule_assurance')) {
                $table->string('matricule_assurance')->nullable()->after('nom_assure');
            }
            if (!Schema::hasColumn('patients', 'taux_couverture')) {
                $table->unsignedTinyInteger('taux_couverture')->nullable()->default(0)->after('matricule_assurance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['assurance_id']);
            $table->dropColumn([
                'est_assure',
                'assurance_id',
                'nom_assure',
                'matricule_assurance',
                'taux_couverture'
            ]);
        });
    }
};