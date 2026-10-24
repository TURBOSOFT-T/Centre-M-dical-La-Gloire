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
                $table->integer('visite_urgentiste_qte')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            $table->dropColumn('visite_urgentiste_qte');
        });
    }
};
