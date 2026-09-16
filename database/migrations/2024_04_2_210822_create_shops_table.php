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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('email')->unique();
            $table->string("avatar")->nullable(true)->default(null);
            $table->string("cover")->nullable(true)->default(null);
            $table->string('password')->nullable(true)->default(null);
            $table->string('adresse')->nullable(true)->default(null);
          
            $table->string("whatsapp")->nullable(true)->default(null);
            $table->string("location")->nullable(true)->default(null);
            $table->string('phone')->nullable(true)->default(null);
            $table->string('code_postal')->nullable(true)->default(null);
            $table->enum("role", ["personnel", "seller"])->default("seller");
            $table->string('two_factor_code')->nullable();
            $table->dateTime('two_factor_expires_at')->nullable();
            $table->enum("statut", ["disponible", "indisponible"])->default("indisponible");
            $table->boolean('active')->default(false);
            $table->boolean('top')->default(false);
            $table->boolean('isbarn')->default(false);
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
        Schema::dropIfExists('shops');
    }
};
