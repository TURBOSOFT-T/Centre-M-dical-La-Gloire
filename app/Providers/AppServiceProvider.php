<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\HomeComposer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['front.fixe', 'front.index', 'front.shop.index','auth.login','auth.register','front.contact.contact','front.about.about'], HomeComposer::class);
        setlocale(LC_TIME, config('app.locale'));

        DB::listen(function ($query) {
        if ($query->time > 1000) { // Log les requêtes de plus d'une seconde
            \Log::warning('Requête lente détectée: ' . $query->sql . ' Temps: ' . $query->time . 'ms');
        }
    });

    }
}
