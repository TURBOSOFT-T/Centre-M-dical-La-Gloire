<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            $table->string('bp')->nullable()->after('adresse');
            $table->string('niu')->nullable()->after('bp');
            $table->string('numero_rc')->nullable()->after('niu');
        });
    }

    public function down(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            $table->dropColumn(['bp', 'niu', 'numero_rc']);
        });
    }
};