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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->enum("statut", ['créé', 'attente', 'livraison', 'traitement', 'En cours livraison', 'livrée', 'payée', 'planification', 'retournée'])->default('attente');

            $table->enum("mode", ['espèce', 'paypal', 'carte de credit', 'orange money', 'momo', 'check', 'virement bancaire', 'solde',  'points',])->default('espèce');
            $table->string('phone_paiement', 50)->nullable();
            $table->enum("etat", ["attente", "confirmé", "annulé"])->default("attente");
            $table->boolean('option_payement')->default(false);
            $table->text("note")->nullable()->default(null);
            $table->string("nom")->nullable();
            $table->string("prenom")->nullable();
            $table->float('coupon')->nullable();
            $table->string("adresse")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->string("pays")->nullable();
            $table->string("gouvernorat")->nullable();
            //   $table->decimal("frais", 10,3)->nullable();
            $table->integer('frais')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->unsignedBigInteger('client_id')->nullable()->default(null);
            $table->unsignedBigInteger('commercial_id')->nullable();
            $table->unsignedBigInteger('caisse_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable()->default(null);
            //  $table->unsignedBigInteger('transport_id')->nullable();
            //  $table->integer("montant_points", 10,3)->nullable();
            //  $table->boolean('buy_with_point')->default(false);
            $table->integer('montant_total')->default(0);
            $table->integer('montant_points')->nullable(); // points utilisés
            $table->boolean('buy_with_points')->default(false);
            $table->integer('solde_utilise')->default(0);
            $table->integer('points_utilise')->default(0);
            $table->unsignedBigInteger('transport_id')->nullable();
            $table->enum('type_commande', ['boutique', 'livraison'])
                ->default('boutique');


            $table->softDeletes();
            $table->foreign('commercial_id')->references('id')->on('users')->onDelete('cascade');



            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
