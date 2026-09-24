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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('prenom')->nullable()->default(null);
            $table->string('phone')->unique(); // Unique et obligatoire$table->string('email')->nullable()->unique()->default(null);
            $table->string('avatar')->nullable()->default(null);
            $table->string('password')->nullable()->default(null);
            $table->string('adresse')->nullable()->default(null);
            $table->string('two_factor_code')->nullable();
            $table->dateTime('two_factor_expires_at')->nullable();
            $table->string('code_postal')->nullable()->default(null);

            // Ajout de 'client' et 'patient' dans les valeurs acceptées
            $table->enum('role', [
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
                'client',
                'patient',
                'user'
            ])->default('accueil');

            $table->integer('points')->default(0);
            $table->integer('solde')->nullable()->default(null);
            $table->string('token')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
