<?php

namespace App\Console\Commands;

use App\Models\promotions;
use Illuminate\Console\Command;
use App\Models\Promotion;
use Carbon\Carbon;

class CheckPromotions extends Command
{
    protected $signature = 'promotions:check';
    protected $description = 'Check and update promotions status';
    public function handle()
    {
        $now = Carbon::now();
        $promotions = promotions::where('fin', '<=', $now)->where('statut', '!=', 'Terminer')->get();

        foreach ($promotions as $promotion) {
            // Terminer la promotion si elle est arrivée à sa fin
            $promotion->update(['statut' => 'Terminer']);
        }

        // Vous pouvez également ajouter du code pour lancer des promotions ici si nécessaire

        $this->info('Promotions checked and updated successfully.');
    }
}
