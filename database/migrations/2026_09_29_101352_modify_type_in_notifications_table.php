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
        Schema::table('notifications', function (Blueprint $table) {
            // Si vous préférez garder un enum et juste ajouter les valeurs :
            // $table->enum('type', ['commande', 'message', 'stock', 'signalment', 'tchat', 'consultation_creation', 'consultation_modification'])->default('commande')->change();
            
            // RECOMMANDÉ : Transformer en string pour une flexibilité totale
            $table->string('type')->default('commande')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['commande', 'message', 'stock', 'signalment', 'tchat'])->default('commande')->change();
        });
    }
};