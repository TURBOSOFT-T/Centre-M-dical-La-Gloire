<?php


namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

//require './vendor/autoload.php';


use App\Models\{commandes, config, User, produits, Category, Service, Marque, Testimonial, views, Sous_category, Famille};
use App\Models\Banners;
use App\Models\templates;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Front\SearchRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{



    public function home(Request $request)
    {

        $categoryProducts = DB::table('produits')
            ->select('*')
            ->where('category_id', 'category_id')
            ->get();


        $banners = Banners::select("titre", "sous_titre", "image")->get();


        return view('front.index', compact('banners',  'categoryProducts'));
    }



    public function shop(Request $request)
    {
        $query = produits::query();

        // CATEGORY
        if ($request->id_categorie) {
            $query->where('category_id', $request->id_categorie);
        }

        // Marque
        if ($request->marque_id) {
            $query->where('marque_id', $request->marque_id);
        }
        // SOUS-CATEGORY
        if ($request->sous_categorie_id) {
            $query->where('sous_categorie_id', $request->sous_categorie_id);
        }
        // FAMILLE
        if ($request->famille_id) {
            $query->where('famille_id', $request->famille_id);
        }


        // SEARCH
        if ($request->key) {
            $key = $request->key;

            $query->where(function ($q) use ($key) {
                $q->where('nom', 'like', "%$key%")
                    ->orWhere('description', 'like', "%$key%");
            });
        }

        // HIGHLIGHT FILTER

        // 🔥 FILTERS
        $highlight = $request->input("highlight");
        if ($highlight === 'promo') {
            $query->whereNotNull('id_promotion');
        }

        if ($highlight === 'is_new') {
            $query->where('is_new', 1);
        }

        if ($highlight === 'best') {
            $query->withSum('vendus', 'quantite')
                ->orderByDesc('vendus_sum_quantite')
                ->having('vendus_sum_quantite', '>', 0);
        }

        if ($highlight === 'most_viewed') {
            $query->withCount('vues')
                ->orderByDesc('vues_count')
                ->having('vues_count', '>', 0);
        }





        $produits = $query->paginate(24);

        return view('front.shop.index', [
            'produits' => $produits,
            'categories' => Category::has('produits')->get(),
            'marques' => Marque::has('produits')->get(),
            'sous_categories' => Sous_category::has('produits')->get(),

            'highlight' => $request->highlight,

        ]);
    }


    public function resetShop()
    {
        session()->forget([
            'key',
            'id_categorie',
            'highlight',
            'sort_by',
            'min_price',
            'max_price',
            'marque_id',
            'sous_categorie_id',
            'famille_id'
        ]);

        return redirect()->route('shop');
    }

    
public function details($id)
{
    $produit = produits::findOrFail($id);

    // 1. Récupération et nettoyage du numéro WhatsApp
    $config = config::first();
    $rawPhone = $config->whatsapp ?? $config->telephone ?? '237600000000';
    $whatsappNumber = preg_replace('/[^0-9]/', '', $rawPhone);

    // Sécurité indicatif : Ajoute automatiquement 237 si le numéro est saisi sans indicatif (ex: 6XXXXXXXX)
    if (strlen($whatsappNumber) === 9 && str_starts_with($whatsappNumber, '6')) {
        $whatsappNumber = '237' . $whatsappNumber;
    }

    // 2. URL absolue de la page produit
    $productUrl = route('details-produits', [
        'id'   => $produit->id,
        'slug' => \Illuminate\Support\Str::slug($produit->nom)
    ]);

    // 3. URL absolue de l'image du produit
    $photoPath = ltrim($produit->photo, '/');
    if (!str_starts_with($photoPath, 'storage/')) {
        $photoPath = 'storage/' . $photoPath;
    }
    $productImageUrl = asset($photoPath);

    // 4. Message prédéfini (Fallback wa.me)
    $whatsappMessage = "Bonjour,\n\n"
        . "Je souhaite commander ce produit :\n"
        . "📦 *{$produit->nom}*\n"
        . "💰 Prix : {$produit->getPrice()} FCFA\n\n"
        . "🖼️ *Photo du produit :*\n{$productImageUrl}\n\n"
        . "🔗 *Lien du produit :*\n{$productUrl}";

    $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . rawurlencode($whatsappMessage);

    // 5. Produits similaires
    $produitsSimilaires = produits::where('category_id', $produit->category_id)
        ->where('id', '!=', $produit->id)
        ->latest()
        ->take(8)
        ->get();

    return view('front.shop.details', compact(
        'produit',
        'whatsappUrl',
        'whatsappNumber',
        'productUrl',
        'productImageUrl',
        'produitsSimilaires',
        'whatsappMessage'
    ));
}
    ///////////Login///////////////////////////////////////////////////
    public function login()
    {
        return view('auth.login');
    }


    public function print_commande($id)
    {
        $commande = commandes::find($id);
        if (!$commande) {
            abort('404');
        }

        $pdf = PDF::loadView('pdf.commande', compact('commande'));
        return $pdf->download("Facture-#" . $commande->id . ".pdf");
    }


    public function print_bordereau(Request $request)
    {
        $ids = json_decode($request->get('ids'));
        $pdf = PDF::loadView('pdf.bordereau', compact("ids"));
        return $pdf->download("bordereau.pdf");
    }



    public function logout()
    {
        auth()->logout();
        return redirect()->route('home');
    }

    public function verify()
    {
        return view('auth.verify-2fa');
    }

    public function registerverify()
    {
        return view('auth.verify-register-2fa');
    }


    public function inscription()
    {
        return view('front.inscription');
    }


    public function confirmation(Request $request)
    {
        $produit = null;
        if ($request->session()->has('produit')) {
            $produit = Session::get('produit');
        }
        return view('front.confirmation', compact("produit"));
    }
}
