<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. S'assurer que la colonne 'id' existe et est clé primaire
        if (!Schema::hasColumn('patients', 'id')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->id()->first();
            });
        }

        // 2. Supprimer la colonne uuid existante si elle est mal typée/corrompue, puis la recréer proprement
        if (Schema::hasColumn('patients', 'uuid')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }

        Schema::table('patients', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            if (!Schema::hasColumn('patients', 'is_synced')) {
                $table->boolean('is_synced')->default(false)->after('user_id');
            }
        });

        // 3. Remplir TOUTES les lignes avec un UUID valide (string de 36 caractères)
        DB::table('patients')->get()->each(function ($patient) {
            DB::table('patients')->where('id', $patient->id)->update([
                'uuid' => (string) Str::uuid()
            ]);
        });

        // 4. Rendre l'UUID non nullable et unique
        Schema::table('patients', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'is_synced']);
        });
    }
};