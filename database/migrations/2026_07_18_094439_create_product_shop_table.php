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
        Schema::create('product_shop', function (Blueprint $table) {
            $table->id();

              $table->unsignedBigInteger('produit_id')->nullable()->default(null);
            $table->unsignedBigInteger('shop_id')->nullable()->default(null);


            $table->integer('stock_particulier')->default(0);
            $table->timestamps();

            // Empêche les doublons : un produit n'a qu'une seule ligne par shop
            $table->unique(['produit_id', 'shop_id']);
         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_shop');
    }
};
