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
        Schema::create('historique_points', function (Blueprint $table) {
            $table->id();
           $table->integer("montant")->nullable();
            $table->unsignedBigInteger("commande_id");
            $table->unsignedBigInteger('produit_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();


            //relations
            $table->foreign('commande_id')->references('id')->on('commandes')->onDelete('cascade');
            $table->foreign('produit_id')->references('id')->on('produits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_points');
    }
};
