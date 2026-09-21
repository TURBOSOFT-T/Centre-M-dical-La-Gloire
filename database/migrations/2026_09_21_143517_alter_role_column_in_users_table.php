<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Mettre à jour temporairement les rôles inexistants (ex: 'client') vers 'user' ou 'accueil'
        DB::statement("UPDATE users SET role = 'user' WHERE role NOT IN (
            'admin', 'medecin', 'infirmier', 'caisse', 'comptable', 'secretaire', 
            'accueil', 'pharmacien', 'laborantin', 'personnel', 'commercial', 
            'gerant', 'vendeur', 'user'
        )");

        // 2. Modifier la structure ENUM de la colonne
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin',
            'medecin',
            'infirmier',
            'caisse',
            'comptable',
            'secretaire',
            'accueil',
            'pharmacien',
            'laborantin',
            'personnel',
            'commercial',
            'gerant',
            'vendeur',
            'user'
        ) NOT NULL DEFAULT 'accueil'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'personnel',
            'admin',
            'client',
            'caisse',
            'commercial',
            'user',
            'gerant',
            'vendeur'
        ) NOT NULL DEFAULT 'client'");
    }
};