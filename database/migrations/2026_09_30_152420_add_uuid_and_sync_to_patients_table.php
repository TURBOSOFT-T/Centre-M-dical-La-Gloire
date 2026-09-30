<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Patient; // Assurez-vous d'importer votre modèle

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // 1. Ajouter les colonnes (nullable temporairement pour éviter les erreurs sur les lignes existantes)
            $table->uuid('uuid')->nullable()->after('id');
            $table->boolean('is_synced')->default(false)->after('user_id');
        });

        // 2. Remplir un UUID pour les patients qui existent déjà dans la base de données
        Patient::whereNull('uuid')->get()->each(function ($patient) {
            $patient->update(['uuid' => (string) Str::uuid()]);
        });

        // 3. Rendre la colonne 'uuid' unique et non-nullable une fois remplie
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
