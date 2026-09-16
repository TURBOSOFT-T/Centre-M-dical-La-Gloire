<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On utilise du SQL brut car modifier un ENUM natif avec Blueprint nécessite une dépendance externe (dbal)
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('commande', 'message', 'stock', 'signalment', 'tchat') DEFAULT 'commande'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En cas de retour en arrière (rollback), on remet l'ancienne énumération
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('commande', 'message', 'stock', 'signalment') DEFAULT 'commande'");
    }
};