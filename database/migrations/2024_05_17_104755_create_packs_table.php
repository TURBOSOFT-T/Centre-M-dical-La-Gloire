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
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string("nom");
            $table->decimal('prix', 13, 3);
            $table->string("reference");
            $table->string("photo")->nullable();
            $table->unsignedBigInteger("by")->nullable();
            $table->timestamps();


            //relation
            $table->foreign("by")->references("id")->on("users")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packs');
    }
};
