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
        Schema::create('historiques_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_produit");
               $table->unsignedBigInteger("shop_id")->nullable();
       
            $table->integer("quantite");
            $table->enum("type", ["Entrant","Sortant"])->default("Entrant");
            $table->timestamps();

            //relation cascade
            $table->foreign("id_produit")->references("id")->on("produits")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiques_stocks');
    }
};
