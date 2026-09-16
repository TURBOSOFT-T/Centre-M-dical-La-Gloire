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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string("titre")->nullable();
            $table->text("code")->nullable();
            $table->text("meta")->nullable();
            $table->string("reference")->nullable();
            $table->unsignedBigInteger('id_produit')->nullable();
            $table->unsignedBigInteger('id_domaine')->nullable();
            $table->boolean("meta_error")->default(false);
            $table->timestamps();

            //relation
            $table->foreign('id_produit')->references('id')->on('produits')->onDelete('set null');
            $table->foreign('id_domaine')->references('id')->on('domaines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
