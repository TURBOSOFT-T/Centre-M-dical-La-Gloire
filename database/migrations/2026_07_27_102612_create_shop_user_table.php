<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Modification : par défaut 'caisse' au lieu de 'seller'
            $table->string('role_in_shop')->default('caisse');
            $table->boolean('is_active')->default(true);

            // Champ optionnel si le rôle dépend du shop spécifique
            //   $table->string('role_in_shop')->nullable(); 

            $table->timestamps();

            // Évite les doublons d'association
            $table->unique(['shop_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_user');
    }
};
