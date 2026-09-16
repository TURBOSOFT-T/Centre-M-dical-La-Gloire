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
        Schema::create('produits', function (Blueprint $table) {

            $table->id();

            $table->string('nom');

            $table->text('description')->nullable();
            $table->text('meta_description')->nullable();

            $table->string('reference')->unique();

            $table->integer('prix');

            $table->integer('prix_achat');

            $table->integer('points')->default(0);

            // image principale
            $table->string('photo')->nullable();

            // galerie images
            $table->json('photos')->nullable();
            $table->string('taille')->nullable();
            $table->string('couleur')->nullable();
            $table->boolean('avec_commission')->default(false);
            // On le met en nullable car si 'avec_commission' est false, ce champ sera vide
            $table->integer('commission')->nullable(); // Utilise decimal('commission', 8, 2) si tu as des centimes
            /*
            |--------------------------------------------------------------------------
            | RELATIONS
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('id_shop')->nullable(); // Doit être nullable !
            $table->unsignedBigInteger('id_promotion')->nullable();

            $table->unsignedBigInteger('category_id')->nullable();

            $table->unsignedBigInteger("sous_category_id")->nullable();
            $table->unsignedBigInteger("sous_categorie_id")->nullable();
            $table->unsignedBigInteger("famille_id")->nullable();
            $table->unsignedBigInteger('marque_id')->nullable();

            // stock
            $table->integer('stock')->default(0);

            // statut
            $table->enum('statut', [
                'disponible',
                'indisponible'
            ])->default('indisponible');

            // options
            $table->boolean('top')->default(false);

            $table->boolean('active')->default(false);

            $table->boolean('new')->default(false);
            $table->boolean('is_new')->default(false);

            $table->boolean('free_shipping')->default(false);

            $table->softDeletes();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEYS
            |--------------------------------------------------------------------------
            */

     

            $table->foreign('id_promotion')
                ->references('id')
                ->on('promotions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
