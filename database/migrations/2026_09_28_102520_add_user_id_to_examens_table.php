<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examens', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('caracteristiques')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('examens', function (Blueprint $type) {
            $table->dropColumn('type');
            $table->dropColumn('user_id');
        });
    }
};