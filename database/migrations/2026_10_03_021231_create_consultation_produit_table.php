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
        Schema::create('consultation_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
        $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
        $table->integer('quantite')->default(1);
        $table->string('voie_administration')->nullable(); // Ex: Orale, IV, IM...
        $table->text('posologie')->nullable(); // Ex: 3 fois par jour pendant 5 jours
        $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_produit');
    }
};
