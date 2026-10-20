<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            // Visite Urgentiste
            $table->integer('visite_urgentiste_qte')->default(0)->after('frais_soins_chambre');
            $table->decimal('visite_urgentiste_pu', 10, 2)->default(0)->after('visite_urgentiste_qte');
            $table->decimal('visite_urgentiste_total', 10, 2)->default(0)->after('visite_urgentiste_pu');

            // Soin Ami
            $table->integer('soin_ami_qte')->default(0)->after('visite_urgentiste_total');
            $table->decimal('soin_ami_pu', 10, 2)->default(0)->after('soin_ami_qte');
            $table->decimal('soin_ami_total', 10, 2)->default(0)->after('soin_ami_pu');
        });
    }

    public function down(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            $table->dropColumn([
                'visite_urgentiste_qte', 'visite_urgentiste_pu', 'visite_urgentiste_total',
                'soin_ami_qte', 'soin_ami_pu', 'soin_ami_total'
            ]);
        });
    }
};