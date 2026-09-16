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
        Schema::create('contenu_packs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_produit");
            $table->unsignedBigInteger("id_pack");
            $table->integer("quantite");
            $table->decimal('benefice', 13, 3)->nullable();
            $table->timestamps();
            $table->softDeletes();


            //relation
            $table->foreign('id_produit')->references('id')->on('produits')->onDelete('cascade');
            $table->foreign('id_pack')->references('id')->on('packs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenu_packs');
    }
};
