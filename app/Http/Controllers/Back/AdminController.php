<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;


use App\Models\commandes;
use App\Models\historiques_stock;
use App\Models\config;
use App\Models\historiques_connexion;
use App\Models\{produits, Category, Marque, Contact, favoris, Coupon, Message, Shop, Testimonial, Visitor, Sous_category};
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
use App\Mail\{OrderChangeStatut, ChangeStatut};
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    use ListGouvernorats;

    /////////////////Transports////////////////

    public function transports()
    {
        return view('admin.transports.list');
    }



    public function favories()
    {
        $favorie = favoris::where('user_id', auth()->id())->get();
        return view('front.comptes.favoris', compact('favoris'));
    }

    public function admin_contact_form()
    {
        $contacts = Contact::paginate(100);
        return view('admin.contacts.list', compact('contacts'));
    }

    public function supprimer_messages(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->back()->with('success', 'Message supprimé avec succès');;
    }

    public function dashboard(Request $request)
    {

        //veification des permissions
        if (auth()->user()->can('dashboard')) {
        } elseif (auth()->user()->can('product_view')) {
            return redirect()->route('produits');
        } elseif (auth()->user()->can('order_view')) {
            return redirect()->route('commandes');
        } elseif (auth()->user()->can('clients_view')) {
            return redirect()->route('clients');
        } else {
            // return "Veuillez demande a l'administrateur de vous attribuer des permissions.";
            return redirect('/');
        }


        $currentYear = date('Y');

        $currentYear2 = Carbon::now()->year;


        // Format ISO 8601 (YYYY-MM-DD)
        $firstDayOfYearISO = Carbon::createFromDate($currentYear2, 1, 1)->startOfDay()->format('Y-m-d');
        $lastDayOfYearISO = Carbon::createFromDate($currentYear2, 12, 31)->endOfDay()->format('Y-m-d');



        $date_debut = $request->input('date_debut') ??  $firstDayOfYearISO;
        $date_fin = $request->input('date_fin') ?? $lastDayOfYearISO;


        //get statistiques
        $visitsPerMonth = [];
        $commandesPerMonth = [];
        $ventesPerMonth = [];
        $inscriptionMonth = [];
        $stat_commande_confirmer_Month = [];
        $stat_commande_non_confirmer_Month = [];
        $profilNet = [];
        for ($i = 1; $i <= 12; $i++) {
            $visitsPerMonth[] = Views::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->count();
            $commandesPerMonth[] = Commandes::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)->count();

            $stat_commande_confirmer_Month[] =  Commandes::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->where('etat', 'confirmé')
                ->count();

            $stat_commande_non_confirmer_Month[] =  Commandes::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->where('etat', 'annulé')
                ->count();

            $montant = Commandes::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->where('statut', 'payée')
                ->get()
                ->sum(function ($commande) {
                    return $commande->montant();
                });
            $inscriptionMonth[] = User::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->count();

            $ventesPerMonth[] = $montant;


            //calcul du profil net de tous les produit
            $stat_commande_non_confirmer_Month[] =  Commandes::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->where('etat', 'annulé')
                ->count();
            $profilNet[] = contenu_commande::whereBetween('created_at', [$date_debut, $date_fin])
                ->whereMonth('created_at', $i)
                ->whereHas('commandes', function ($query) {
                    $query->where('statut', 'payée');
                })
                ->sum('benefice');
        }


        $total_visites = views::whereBetween('created_at', [$date_debut, $date_fin])->count();
        $total_commandes = commandes::whereBetween('created_at', [$date_debut, $date_fin])->where('statut', 'payée')->get(['id']);
        $total_produits = produits::count();
        $totalDesCommandes = 0;
        foreach ($total_commandes as $command) {
            $totalDesCommandes += $command->montant();
        }
        $totalUser = User::whereBetween('created_at', [$date_debut, $date_fin])->count();

        $totalVisitors = Visitor::count();
        $todayVisitors = Visitor::whereDate('created_at', today())->count();
        $weekVisitors = Visitor::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        $monthVisitors = Visitor::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        //get all command group by gouvernorrant collun Asc 
        $top_gouvernorat = [];
        $gouvernorats = commandes::select('gouvernorat', DB::raw('COUNT(*) as count'))
            ->whereBetween('commandes.created_at', [$date_debut, $date_fin])
            ->groupBy('gouvernorat')
            ->orderBy('count', 'desc')
            ->get();
        foreach ($gouvernorats as $top) {
            $top_gouvernorat[] = [
                "nom" => $top->gouvernorat,
                "total" => $top->count,
                "montant" => 0,
            ];
        }



        $commandes = DB::table('commandes')
            ->selectRaw('statut, COUNT(*) as count')
            ->whereBetween('created_at', [$date_debut, $date_fin])
            ->where(function ($query) {
                $query->where('statut', '!=', 'créé')
                    ->orWhere(function ($query) {
                        $query->where('statut', 'créé')
                            ->where('etat', 'confirmé');
                    });
            })
            ->groupBy('statut')
            ->whereIn('statut', ['créé', 'traitement', 'livraison', 'livrée', 'payée', 'planification retour', 'retournée'])
            ->get()
            ->pluck('count', 'statut')
            ->toArray();

        $nombre_total_commande = commandes::whereBetween('created_at', [$date_debut, $date_fin])->count();
        $statistique_commandes_graph = [];
        if ($nombre_total_commande > 0) {
            foreach ($commandes as $key => $coms) {
                $statistique_commandes_graph[] = [
                    "statut" => $key,
                    "valeur" => $coms,
                    "pourcentage" => round((($coms / $nombre_total_commande) * 100), 2),
                ];
            }
        }

        $etat_commandes = commandes::selectRaw('etat, COUNT(*) as count')
            ->whereBetween('created_at', [$date_debut, $date_fin])
            ->groupBy('etat')
            ->whereIn('etat', ['confirmé', 'annulé', 'attente'])
            ->get()
            ->pluck('count', 'etat')
            ->toArray();
        // Vérifiez si $etat_commandes est défini, sinon définissez-le comme un tableau vide
        $etat_commandes = $etat_commandes ?? [];
        $pourcentage_confirmer = 0;
        $pourcentage_non_confirmer = 0;
        $pourcentage_attente = 0;

        $total_confirme = $etat_commandes['confirmé'] ?? 0;
        $total_non_confirme = $etat_commandes['annulé'] ?? 0;
        $total_attente = $etat_commandes['attente'] ?? 0;
        $total_commandes = $total_confirme + $total_non_confirme +  $total_attente;
        if ($total_commandes != 0) {
            $pourcentage_confirmer = round(($total_confirme / $total_commandes) * 100);
            $pourcentage_non_confirmer = round(($total_non_confirme / $total_commandes) * 100);
            $pourcentage_attente = 100 - $pourcentage_confirmer - $pourcentage_non_confirmer;
        }

        $etat_commandes = [
            'confirmer' => $total_confirme,
            'non-confirmer' => $total_non_confirme,
            'attente' => $total_attente,

            'pourcentage_confirmer' => $pourcentage_confirmer,
            'pourcentage_non-confirmer' => $pourcentage_non_confirmer,
            'pourcentage_attente' => $pourcentage_attente,

        ];


        $json_commandes = '[' . implode(',', [$commandes['créé'] ?? 0, $commandes['traitement'] ?? 0, $commandes['livraison'] ?? 0, $commandes['livré'] ?? 0, $commandes['payée'] ?? 0, $commandes['planification retour'] ?? 0, $commandes['retournée'] ?? 0]) . ']';

        $request->session()->flash('date_debut', $request->input('date_debut'));
        $request->session()->flash('date_fin', $request->input('date_fin'));
        $messages = Message::orderBy('created_at', 'desc')
            ->take(5) // Récupère uniquement les 5 derniers messages reçus
            ->get();
        return view('admin.index')
            ->with("messages", $messages)
            ->with("totalUser", $totalUser)
            ->with('visitsPerMonth', $visitsPerMonth)
            ->with('commandesPerMonth', $commandesPerMonth)
            ->with('ventesPerMonth', $ventesPerMonth)
            ->with('total_visites', $total_visites)
            ->with('total_commandes', $total_commandes)
            ->with('total_produits', $total_produits)
            ->with("statistique_commandes_graph", $statistique_commandes_graph)
            ->with("commandes", $commandes)
            ->with('nombre_total_commande', $nombre_total_commande)
            ->with("json_commandes", $json_commandes)
            ->with('inscriptionMonth', $inscriptionMonth)
            ->with('profilNet', $profilNet)
            ->with('stat_commande_non_confirmer_Month', $stat_commande_non_confirmer_Month)
            ->with('stat_commande_confirmer_Month', $stat_commande_confirmer_Month)
            ->with('etat_commandes', $etat_commandes)
            ->with('top_gouvernorat', $top_gouvernorat)
            ->with('totalDesCommandes', $totalDesCommandes)


            ->with('totalVisitors', $totalVisitors);
    }



    public function update_config(Request $request)
    {
        $send_mail_update_commande = $request->input('send_mail_update_commande') ? 1 : 0;
        $config = config::first();
        $config->send_mail_update_commande = $send_mail_update_commande;
        $config->save();

        return redirect()
            ->route('commandes')
            ->with('success', 'Configuration mise à jour avec succès');
    }



    public function new_commande()
    {
        return view("admin.commandes.ajouter");
    }


    public function add_note(Request $request)
    {
        $id_commande = $request->input('id_commande');
        $note = $request->input("note");

        $commande = commandes::find($id_commande);
        $commande->note = $note;
        if (!$commande) {
            return redirect()
                ->route("commandes")
                ->with("error", "Commande introuvable!");
        }
        $commande->save();
        return redirect()
            ->route("commandes")
            ->with("success", "La note a été ajouté a la commande.");
    }


    public function corbeille()
    {
        return view("admin.produits.corbeille");
    }


    public function corbeillepersonnel()
    {
        return view("admin.personnels.corbeille");
    }

    public function export_clients()
    {
        $users = clients::select('nom', 'phone', 'adresse', 'pays', 'gouvernorat')
            ->get();
        return Excel::download(new ExportUser($users), 'users.xlsx');
    }



    public function live_notifications()
    {
        $total = notifications::where("statut", "unread")->count();
        return response()->json(
            [
                'total' => $total
            ]
        );
    }




    public function commandes_en_lives()
    {
        return view('admin.commandes.lives');
    }
    public function commandes_commercial()
    {
        return view('admin.commandes.list_commercial');
    }

    public function edit_commande($id)
    {
        $commande = commandes::find($id);
        if (!$commande) {
            $message = "Commande introuvable";
            abort('404', $message);
        }
        if ($commande->statut == 'retournée' && $commande->statut == 'payée' && $commande->statut == 'traitement') {

            $this->sendOrderConfirmationMail($commande);
            return redirect()->route('commandes');
        }
        $this->sendOrderConfirmationMail($commande);


        return view('admin.commandes.edit', compact('commande'));
    }

    public function sendOrderConfirmationMail($commande)
    {
        //  Mail::to ($commande->email)->send(new OrderChangeStatut($commande));
    }
    //////////Categories/////////////
    public function category_add()
    {
        return view('admin.categories.add');
    }

    public function categories()
    {
        return view('admin.categories.list');
    }

    public function categories_update($id)
    {
        $category = Category::find($id);
        if (!$category) {
            $message = "Category non disponible !";
            abort(404, $message);
        }
        return view('admin.categories.update', compact('category'));
    }



    //////////////////Support client

    public function support()
    {
        return view('admin.supports.list');
    }


    ///////////////Marques/////////////////////

    public function marques()
    {
        return view('admin.marques.list');
    }



    ////////////////coupons //////////////////

    public function coupons()
    {
        $coupons = Coupon::orderBy('id', 'DESC')->paginate('10');
        return view('admin.coupons.list', compact('coupons'));
    }

    public function coupon_add()
    {
        $commercials = User::where('role', 'commercial')->get();
        return view('admin.coupons.add', compact('commercials'));
    }

    public function coupons_update($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            $message = "Coupon non disponible !";
            abort(404, $message);
        }
        return view('admin.coupons.update', compact('coupon'));
    }


    ///////////////Testimonials////////////

    public function testimonials()
    {
        $testimonials = Testimonial::paginate(10);
        return view('admin.testimonials.list', compact('testimonials'));
    }




    //////////Produits/////////////////
    public function produit_add()
    {
        return view('admin.produits.add');
    }

    public function produits()
    {
        return view('admin.produits.list');
    }

    public function produits_update($id)
    {
        $produit = produits::find($id);
        if (!$produit) {
            $message = "Produit non disponible !";
            abort(404, $message);
        }
        return view('admin.produits.update', compact('produit'));
    }


    public function historique($id)
    {
        $produit = produits::find($id);
        if (!$produit) {
            $message = "Produit non disponible !";
            abort(404, $message);
        }
        return view('admin.produits.historique', compact('produit'));
    }



    public function commandes()
    {
        return view('admin.commandes.list');
    }

    public function parametres()
    {
        $connexions = historiques_connexion::Orderby("id", "Desc")
            ->where('user_id', Auth::id())
            ->get();

        $ipAddress = request()->ip();
        return view('admin.parametres.index', compact('connexions'));
    }


    public function personnels()
    {
        // $personnels = User::where('role', 'personnel')->get();
            $total_supprimers = User::onlyTrashed()->count();
        $personnels = User::whereNotIn('role', ['client', 'admin'])->get();
        return view('admin.personnels.list', compact('personnels', 'total_supprimers'));
    }



    public function shops()
    {
        $shops = Shop::all();
        return view('admin.shops.list', compact('shops'));
    }


    public function updateRoleShop(Request $request, $id)
    {
        // Validation
        $request->validate([
            'role' => 'required|string'
        ]);

        // Recherche utilisateur
        $user = Shop::findOrFail($id);

        // Mise à jour
        $user->role = $request->role;
        $user->save();

        // Redirection + message
        return redirect()->back()->with('success', 'Rôle mis à jour avec succès');
    }

    public function toggleStatus($id)
    {
        // 1. Récupérer la boutique ou renvoyer une erreur 404 si elle n'existe pas
        $shop = Shop::findOrFail($id);

        // 2. Inverser le statut (si vrai devient faux, si faux devient vrai)
        $shop->is_active = !$shop->is_active;

        // 3. Sauvegarder les modifications en base de données
        $shop->save();

        // 4. Déterminer le message en fonction du nouvel état
        $statusMessage = $shop->is_active
            ? "La boutique '{$shop->name}' a été activée avec succès."
            : "La boutique '{$shop->name}' a été désactivée avec succès.";

        // 5. Rediriger l'utilisateur avec un message d'alerte flash
        return redirect()->back()->with('success', $statusMessage);
    }


    public function details_commande($id)
    {
        $commande = commandes::find($id);
        if (!$commande) {
            $message = "Commande introuvable !";
            abort(404, $message);
        }
        return view('admin.commandes.details', compact('commande'));
    }



    public function promotions($id = null)
    {
        if ($id !== null) {
            $produit = Produits::find($id);
            if (!$produit) {
                abort(404);
            }
        } else {
            $produit = null;
        }
        return view('admin.promotions.index', compact('produit'));
    }


    public function clients()
    {
        $clients = User::where('role', 'client')->get();
        $comptes = clients::all();
        return view('admin.clients.list', compact('clients', 'comptes'));
    }

    public function comptes()
    {
        $clients = User::where('role', 'client')->get();
        $comptes = User::all();
        return view('admin.comptes.list', compact('clients', 'comptes'));
    }



    public function contact_admin()
    {
        return view('admin.parametres.contact');
    }

    public function delete_personnel($id)
    {
        $user = User::where("id", '=', $id)->first();
        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'Personnel supprimé avec succès!');
        }
    }


    public function updateRole(Request $request, $id)
    {
        // Validation
        $request->validate([
            'role' => 'required|string'
        ]);

        // Recherche utilisateur
        $user = User::findOrFail($id);

        // Mise à jour
        $user->role = $request->role;
        $user->save();

        // Redirection + message
        return redirect()->back()->with('success', 'Rôle mis à jour avec succès');
    }

    public function update_permission(Request $request)
    {

        $selectedPermissions = $request->input('permissions', []);
        $user = User::findOrFail($request->input('id'));
        $user->syncPermissions($selectedPermissions);
        return redirect()
            ->back()
            ->with('success', 'Permissions mises à jour avec succès.');
    }
}
