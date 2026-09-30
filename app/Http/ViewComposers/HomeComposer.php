<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

use Illuminate\Support\Facades\DB;

class HomeComposer
{

  public function compose(View $view)
  {
    $view->with([

  
    

      ////////////////Langues et traductions
      'locales' => [
        'fr' => ['name' => 'Français', 'flag' => 'https://img.icons8.com/color/20/france-circular.png'],
        'en' => ['name' => 'English', 'flag' => 'https://img.icons8.com/color/20/great-britain-circular.png'],

      ],
      'currentLocale' => app()->getLocale(),

      'config' => DB::table('configs')->first(),



    ]);
  }
}
