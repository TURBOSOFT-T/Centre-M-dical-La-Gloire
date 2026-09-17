<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom')->nullable(true)->default(null);
            $table->string('phone')->unique(); // Unique et obligatoire pour servir d'identifiant de connexion
            $table->string('email')->nullable()->unique()->default(null); // Devient nullable pour les inscriptions par téléphone
            $table->string("avatar")->nullable(true)->default(null);
            $table->string('password')->nullable(true)->default(null);
            $table->string('adresse')->nullable(true)->default(null);
            $table->string('two_factor_code')->nullable();
            $table->dateTime('two_factor_expires_at')->nullable();
            $table->string('code_postal')->nullable(true)->default(null);
            $table->enum("role", [
                "personnel",
                "admin",
                "client",
                "caisse",
                'commercial',
                'user',
                'gerant',
                'vendeur'
            ])->default("client");
            $table->integer("points")->default(0);
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
