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
        Schema::table('hospitalisations', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitalisations', 'complication')) {
                 $table->decimal('visite_urgentiste_pu', 10, 2)->default(0);
            $table->decimal('visite_urgentiste_total', 10, 2)->default(0);

            // Soin Ami
            $table->integer('soin_ami_qte')->default(0);
            $table->decimal('soin_ami_pu', 10, 2)->default(0);
            $table->decimal('soin_ami_total', 10, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
           $table->dropColumn('visite_urgentiste_pu');
            $table->dropColumn('visite_urgentiste_total');
             $table->dropColumn('soin_ami_qte');
              $table->dropColumn('soin_ami_pu');
               $table->dropColumn('soin_ami_total');
        });
    }
};
