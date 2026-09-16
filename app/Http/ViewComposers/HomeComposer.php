<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\{Category,  produits, favoris, Testimonial};
use Illuminate\Support\Facades\DB;

class HomeComposer
{

  public function compose(View $view)
  {
    $view->with([

      'categories' => Category::has('produits')->take(10)->get(), // Pour la catégorie page

      'searchproducts' => produits::select('*')->latest()->take(5)->get(),
      'lastproduits' => produits::orderBy('created_at', 'desc')->take(12)->get(),
    
      'produitshome' => produits::select('*')->latest()->take(12)->get(),
      'testimonials' => Testimonial::orderBy('created_at', 'desc')
        ->where('active', '1')
        ->limit(30)->get(),

      ////////////////Langues et traductions
      'locales' => [
        'fr' => ['name' => 'Français', 'flag' => 'https://img.icons8.com/color/20/france-circular.png'],
        'en' => ['name' => 'English', 'flag' => 'https://img.icons8.com/color/20/great-britain-circular.png'],

      ],
      'currentLocale' => app()->getLocale(),

      'config' => DB::table('configs')->first(),

      'favoris' => Favoris::where('id_produit', '!=', null)
        ->where('id_user', auth()->id())->get(),

      'produitsPromo' => produits::whereNotNull('id_promotion')
        ->latest()
        ->take(6)
        ->get(),

      'produitsPromos' => produits::whereNotNull('id_promotion')
        ->latest()
        ->take(20)
        ->get(),

      // 🆕 NOUVEAUTÉS
      'nouveautes' => produits::where('is_new', 1)
        ->latest()
        ->take(20)
        ->get(),

      // 🕒 LES PLUS VUS
      'mostViewed' => produits::withCount('vues')
        ->orderByDesc('vues_count')
        ->having('vues_count', '>', 0)
        ->take(20)
        ->get(),

      // 🏆 BEST SELLERS
      'bestSellers' => produits::withSum('vendus', 'quantite')
        ->orderByDesc('vendus_sum_quantite')
        ->having('vendus_sum_quantite', '>', 0)
        ->take(20)
        ->get(),
    ]);
  }
}
