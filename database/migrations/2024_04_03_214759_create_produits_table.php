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

            // Informations générales
            $table->string('nom'); // Ex: Paracétamol Biogaran 500mg
            $table->string('reference')->unique()->nullable(); // Code CIP, Code Barre / EAN
            $table->text('description')->nullable();
            $table->text('meta_description')->nullable();

            // Spécificités Médicales / Pharmacie
            $table->boolean('avec_dci')->default(false);
            $table->string('dci')->nullable(); // Dénomination Commune Internationale (ex: Paracétamol)
            $table->integer('grammage')->nullable(); // Ex: 500mg, 1g
            $table->string('voie')->nullable(); // Ex: Comprimé sécable, Sirop, Voie orale, Injectable
            $table->boolean('sur_ordonnance')->default(false); // Si le médicament nécessite une prescription

            // Tarification & Fidelité
            $table->integer('prix'); // Prix de vente public (FCFA)
            $table->integer('prix_achat')->default(0); // Prix d'achat fournisseur (FCFA)
            $table->integer('points')->default(0); // Points de fidélité

            // Images & Médias
            $table->string('photo')->nullable(); // Photo principale
            $table->json('photos')->nullable(); // Galerie photos secondaires

            // Gestion du Stock & Traçabilité
            $table->integer('stock')->default(0);
            $table->integer('seuil_alerte_stock')->default(5); // Alerte réapprovisionnement
            $table->string('numero_lot')->nullable(); // Numéro de lot fabricant
            $table->date('date_peremption')->nullable(); // Date d'expiration

            // Relations (Clés étrangères)
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('marque_id')->nullable()->constrained('marques')->nullOnDelete(); // Ou laboratoire fabricant
            $table->foreignId('id_promotion')->nullable()->constrained('promotions')->nullOnDelete();
            $table->unsignedBigInteger('id_shop')->nullable();

            // Visibilité & Statuts
            $table->enum('statut', ['disponible', 'indisponible'])->default('disponible');
            $table->boolean('active')->default(true);
            $table->boolean('top')->default(false); // Produit vedette
            $table->boolean('is_new')->default(false); // Nouveauté

            $table->softDeletes(); // Suppression douce
            $table->timestamps();
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