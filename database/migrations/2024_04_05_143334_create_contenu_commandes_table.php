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
        Schema::create('contenu_commandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_commande');
            $table->unsignedBigInteger('id_produit')->nullable();
              $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('id_pack')->nullable();
             $table->unsignedBigInteger('commercial_id')->nullable();
            $table->enum("type",["produit","pack"])->default("produit");
            $table->integer("quantite")->nullable();
              $table->integer('total_gain_points')->default(0);
            $table->integer("quantity")->nullable();
           // $table->decimal('prix_unitaire', 13, 3)->default(10);
           // $table->decimal('prix', 13, 3)->nullable();
           // $table->decimal('benefice', 13, 3)->default();
            $table->integer('prix_unitaire')->nullable();
            $table->integer('prix')->nullable();
            $table->integer('benefice')->default();
             $table->softDeletes();
            $table->timestamps();

            //relation
            $table->foreign('id_commande')->references('id')->on('commandes')->onDelete('cascade');
            $table->foreign('id_produit')->references('id')->on('produits')->onDelete('cascade');
             $table->foreign('commercial_id')->references('id')->on('users')->onDelete('set null');
           // $table->foreign('id_pack')->references('id')->on('packs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenu_commandes');
    }
};
