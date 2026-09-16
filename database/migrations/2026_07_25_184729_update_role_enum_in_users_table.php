<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modification de la colonne ENUM pour inclure tous les rôles possibles
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('personnel', 'admin', 'client', 'caisse', 'commercial', 'user') DEFAULT 'client';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('personnel', 'admin', 'client', 'caisse', 'commercial') DEFAULT 'client';");
    }
};