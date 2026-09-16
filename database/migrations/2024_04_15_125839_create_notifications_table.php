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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->nullable();
            $table->string("url")->nullable();
            $table->string("message")->nullable();
            $table->timestamp('read_at')->nullable();
            $table->enum("type",["commande","message","stock","signalment","tchat"])->default("commande");
            $table->enum("statut",["read","unread"])->default("unread");
            $table->timestamps();


        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
