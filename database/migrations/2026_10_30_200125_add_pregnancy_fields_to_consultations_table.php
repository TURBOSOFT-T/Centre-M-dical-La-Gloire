<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->boolean('is_pregnant')->default(false)->after('patient_id'); // Indique si la patiente est enceinte
            $table->integer('gestational_age_weeks')->nullable()->after('is_pregnant'); // Âge de la grossesse en semaines (SA)
            $table->date('expected_delivery_date')->nullable()->after('gestational_age_weeks'); // Date Probable d'Accouchement (DPA)
            $table->integer('gravida')->nullable()->after('expected_delivery_date'); // Nombre de grossesses (G)
            $table->integer('parity')->nullable()->after('gravida'); // Nombre d'accouchements (P)
            $table->integer('prenatal_visit_number')->nullable()->after('parity'); // Numéro de la CPN (ex: CPN 1, 2, 3...)
            $table->string('fetal_heart_rate')->nullable()->after('prenatal_visit_number'); // Bruits du Cœur Fœtal (BCF)
            $table->text('pregnancy_notes')->nullable()->after('fetal_heart_rate'); // Observations particulières liées à la grossesse
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'is_pregnant',
                'gestational_age_weeks',
                'expected_delivery_date',
                'gravida',
                'parity',
                'prenatal_visit_number',
                'fetal_heart_rate',
                'pregnancy_notes'
            ]);
        });
    }
};