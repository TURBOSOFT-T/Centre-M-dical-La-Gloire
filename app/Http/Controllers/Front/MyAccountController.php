<?php


namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

use App\Models\commandes;
use App\Models\config;
use App\Models\historiques_connexion;
use App\Models\{produits, Category, favoris as ModelsFavoris, Historique_points, Historique_solde};
use App\Models\User;
use App\Models\views;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportUser;
use App\Http\Traits\ListGouvernorats;
use App\Models\clients;
use App\Models\contenu_commande;
use App\Models\domaines;
use App\Models\notifications;
use App\Models\templates;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\{OrderChangeStatuts, ChangeStatut};
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;

class MyAccountController extends Controller
{
    use ListGouvernorats;




    public function comptes()
    {

        $commandes = commandes::where('user_id', auth()->id())->get();
        return view('front.comptes.commandes', compact('commandes'));
    }

    public function favories()
    {

        return view('front.comptes.favories');
    }

    public function profile()
    {
        return view('front.comptes.profile');
    }
    public function account()
    {
        $userId = auth()->id();

        // 📦 Commandes paginées
        $commandes = commandes::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        // ❤️ Favoris paginés
        $favoris = ModelsFavoris::where('id_user', $userId)
            ->latest()
            ->paginate(10);

        // 📊 Commandes livrées ou payées
        $totalCommand = commandes::where('user_id', $userId)
            ->whereIn('statut', ['livrée', 'payée'])
            ->count();

        // ❤️ Total favoris
        $totalFavoris = ModelsFavoris::where('id_user', $userId)
            ->count();

        // 🚚 Commandes en cours
        $commandesEnCours = commandes::where('user_id', $userId)
            ->whereIn('statut', ['attente', 'traitement', 'En cours livraison', 'planification'])
            ->count();

        

        return view('front.comptes.account', compact(
            'commandes',
            'favoris',
            'totalCommand',
            'totalFavoris',
            'commandesEnCours',
        
            
            
            

        ));
    }






    public function commandes()
    {
        return view('comptes.commandes.list');
    }
}
