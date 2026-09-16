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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->integer("pourcentage");
            $table->dateTime("debut")->nullable();
            $table->dateTime("fin")->nullable();
            $table->enum("statut", ["Programmer","En cours", "Terminer"])->default("Programmer");
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
