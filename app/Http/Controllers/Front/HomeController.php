<?php


namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

//require './vendor/autoload.php';


use App\Models\{commandes, config, User, produits, Category, Service, Marque,  views, Sous_category, Famille};
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




    public function home()
    {
        return view('admin.login');
    }



    ///////////Login///////////////////////////////////////////////////
    public function login()
    {
        /*   return view('auth.login'); */

        return view('admin.login');
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
