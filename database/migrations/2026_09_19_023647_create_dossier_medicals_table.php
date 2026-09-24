<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dossiers_medicaux', function (Blueprint $table) {
            $table->id();
            $table->string('code_dossier')->unique(); // Ex: DM-2026-0001
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('groupe_sanguin')->nullable(); // Ex: A+, O-, etc.
            $table->text('antecedents_medicaux')->nullable();
            $table->text('antecedents_chirurgicaux')->nullable();
            $table->text('allergies')->nullable();
            $table->text('traitements_chroniques')->nullable();
            $table->enum('statut', ['actif', 'archive', 'decede'])->default('actif');
            $table->timestamps();
        });

       
    }

    public function down(): void {
        Schema::dropIfExists('dossiers_medicaux');
    }
};