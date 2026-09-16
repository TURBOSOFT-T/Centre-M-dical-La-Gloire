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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

             $table->unsignedBigInteger('user_id'); // Lien avec le client
            $table->integer('montant'); // Montant de la transaction
            $table->enum('type', ['debit', 'credit']); // Type de transaction
            $table->string('description')->nullable(); // Description ou raison de la transaction
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
   
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
