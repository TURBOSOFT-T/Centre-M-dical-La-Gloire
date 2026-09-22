<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            // Exemple : changer le type de dci en string
            $table->string('dci')->nullable()->change();
            $table->string('voie')->nullable()->change();
            $table->integer('grammage')->nullable()->change();

            // Ajouter de nouveaux champs
            $table->integer('seuil_alerte_stock')->default(5)->after('stock');
            $table->boolean('sur_ordonnance')->default(false)->after('avec_dci');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn(['seuil_alerte_stock', 'sur_ordonnance']);
        });
    }
};