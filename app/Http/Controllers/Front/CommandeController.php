<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{commandes, produits, Coupon, clients, contenu_commande, config,  notifications,  Transport, User};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\ListGouvernorats;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class CommandeController extends Controller
{

    public $cart;
    use ListGouvernorats;



    public function commander()
    {
        $configs = config::firstOrFail();
        $paniers_session = session('cart', []);
        //dd($paniers_session);
        // 1. Validation de la session
        if (!is_array($paniers_session) || empty($paniers_session)) {
            request()->session()->flash('error', 'Le panier est vide !');
            return back();
        }

        $paniers = [];
        $total = 0;
        $coupon_value = session()->has('coupon') ? session('coupon')['value'] : 0;

        // 2. Parcours imbriqué pour structure [shop_id][id_produit]
        foreach ($paniers_session as $shop_id => $produits_dans_shop) {

            // Vérification de sécurité : s'assurer que c'est bien un tableau
            if (!is_array($produits_dans_shop)) continue;

            foreach ($produits_dans_shop as $id_produit => $details) {

                $produit = produits::find($id_produit);

                if ($produit) {
                    $quantite = $details['quantite'] ?? 0;
                    $prix_unitaire = $produit->getPrice();
                    $sous_total = $quantite * $prix_unitaire;

                    $paniers[] = [
                        'nom'        => $produit->nom,
                        'id_produit' => $produit->id,
                        'shop_id'    => $shop_id, // Traçabilité magasin
                        'photo'      => $produit->photo,
                        'quantite'   => $quantite,
                        'prix'       => $prix_unitaire,
                        'total'      => $sous_total,
                    ];

                    // Calcul du total avec coupon
                    $total += $sous_total;
                    //   dd($paniers);
                }
            }
        }

        // Application globale du coupon sur le total
        $total = max(0, $total - $coupon_value);

        $gouvernorats = $this->getListGouvernorat();
        $transports = Transport::all();

        return view('front.commandes.checkout', compact('configs', 'paniers', 'total', 'gouvernorats', 'transports'));
    }

    public function confirmOrder(Request $request)
    {
        // 1. Validation stricte
        $data = $request->validate([
            'nom'            => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:50'],
            'phone_paiement' => ['nullable', 'required_if:mode,orange money,momo', 'string', 'max:50'],
            'mode'           => ['required', 'string', 'in:espèce,orange money,momo'],
            'transport_id'   => ['nullable', 'exists:transports,id'],
            'type_commande'  => ['required', 'string', 'in:livraison,boutique'],
            'adresse'        => ['nullable', 'required_if:type_commande,livraison', 'string', 'max:255'],
        ]);

        $paniers_session = session('cart', []);
        if (empty($paniers_session)) {
            return back()->withErrors(['cart' => 'Le panier est vide !']);
        }

        // 2. Calcul du montant total (Parcours multi-magasins)
        $totalCommande = 0;
        foreach ($paniers_session as $shop_id => $produits_du_shop) {
            foreach ($produits_du_shop as $item) {
                $produit = produits::find($item['id_produit']);
                if ($produit) {
                    $totalCommande += $produit->getPrice() * $item['quantite'];
                }
            }
        }

        $transport = ($request->type_commande === 'livraison') ? Transport::find($request->transport_id) : null;
        $totalCommande -= session('coupon')['value'] ?? 0;
        $totalCommande += $transport->frais ?? 0;

        // Récupération éventuelle du commercial lié au coupon actif en session
        $commercialId = null;
        if (session()->has('coupon')) {
            $couponCode = session('coupon')['code'] ?? null;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && $coupon->commercial_id) {
                    $commercialId = $coupon->commercial_id;
                }
            }
        }

        // 3. Gestion Client/Utilisateur
        $connecte = Auth::user();
        $userId = $connecte ? $connecte->id : null;
        $clientId = null;

        $client = clients::updateOrCreate(
            ['phone' => $request->phone],
            [
                'nom'         => $request->nom,
                'adresse'     => $request->adresse ?? null,
                'gouvernorat' => $request->gouvernorat ?? null,
            ]
        );
        $clientId = $client->id;

        // 4. Transaction pour garantir l'intégrité (Stock + Commande)
        $redirectionPaiement = null;

        DB::transaction(function () use ($request, $data, $paniers_session, $totalCommande, $transport, $userId, $clientId, $commercialId, &$redirectionPaiement) {
            $modePaiement = $data['mode'];

            // Si c'est Mobile Money, on met le statut en 'pending' (en attente) jusqu'à confirmation par Webhook
            $statutCommande = in_array($modePaiement, ['orange money', 'momo']) ? 'pending' : 'traitement';

            $order = commandes::create([
                'user_id'        => $userId,
                'client_id'      => $clientId,
                'commercial_id'  => $commercialId,
                'shop_id'        => array_key_first($paniers_session),
                'reference'      => 'SWB-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'nom'            => $request->nom,
                'phone'          => $request->phone,
                'phone_paiement' => $data['phone_paiement'] ?? null,
                'mode'           => $data['mode'],
                'montant_total'  => $totalCommande,
                'transport_id'   => $transport->id ?? null,
                'frais'          => $transport->frais ?? 0,
                'coupon'         => session('coupon')['value'] ?? null,
                'etat'           => 'confirmé',
                'statut'         => $statutCommande,
            ]);

            // 5. Enregistrement contenu et Déstockage
            foreach ($paniers_session as $shop_id => $produits_du_shop) {
                $safeShopId = (int) $shop_id;

                foreach ($produits_du_shop as $item) {
                    $produit = produits::find($item['id_produit']);
                    if (!$produit) continue;

                    contenu_commande::create([
                        'id_commande'   => $order->id,
                        'id_produit'    => $produit->id,
                        'shop_id'       => $safeShopId,
                        'prix_unitaire' => $produit->getPrice(),
                        'quantite'      => $item['quantite'],
                        'benefice'      => ($produit->getPrice() - $produit->prix_achat) * $item['quantite'],
                    ]);

                    $produit->diminuer_stock((int)$item['quantite'], $safeShopId);
                }
            }

            // 6. Envoi du SMS si le type de commande est une livraison
            if ($request->type_commande === 'livraison' || in_array($data['mode'], ['orange money', 'momo'])) {
                $messageTexte = "Bonjour " . $request->nom . ", votre commande " . $order->reference . " a bien été enregistrée. Le service client va vous contacter très prochainement.";

                try {
                    $response = \Illuminate\Support\Facades\Http::get('https://sms.etech-keys.com/ss/api.php', [
                        'login'         => '655262413',
                        'password'      => '655262413',
                        'sender_id'     => 'ETECH KEYS',
                        'destinataire'  => $request->phone,
                        'message'       => $messageTexte,
                        'ext_id'        => $order->id,
                        'programmation' => 0
                    ]);

                    if (!$response->successful()) {
                        Log::error("Erreur HTTP SMS Livraison (Commande ID: " . $order->id . ") : " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("Exception SMS Livraison (Commande ID: " . $order->id . ") : " . $e->getMessage());
                }
            }


            $notification = new notifications();
            $notification->url = route('details_commande', ['id' => $order->id]);
            $notification->titre = "Nouvelle commande.";
            $notification->message = "Commande passée par " . $order->nom;
            $notification->type = "commande";
            $notification->save();

            // Gestion de l'appel CamPay si mobile money sélectionné
            if (in_array($data['mode'], ['orange money', 'momo'])) {
                $numeroPayeur = $data['phone_paiement'] ?? $request->phone;

                $paymentLink = $this->initierPaiementCampay(
                    montant: $totalCommande,
                    telephone: $numeroPayeur,
                    referenceExterne: $order->reference,
                    description: "Commande " . $order->reference
                );

                if ($paymentLink) {
                    $redirectionPaiement = $paymentLink;
                } else {
                    throw new \Exception("Impossible d'initialiser le lien de paiement CamPay.");
                }
            }
        });

        // S'il y a un lien de paiement CamPay généré, on redirige vers l'interface de paiement externe
        if ($redirectionPaiement) {
            return redirect()->away($redirectionPaiement);
        }
        session()->forget(['cart', 'coupon']);
        // Si c'est en espèce
        return redirect()->route('thank-you');
    }


    private function initierPaiementCampay($montant, $telephone, $referenceExterne, $description = "Paiement commande")
    {
        // 1. Formatage strict du numéro au format international
        $phone = preg_replace('/[^0-9]/', '', $telephone);
        if (strlen($phone) === 9) {
            $phone = '237' . $phone;
        }

        try {
            // 2. Étape d'authentification dynamique (qui fonctionne)
            $auth = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post('https://demo.campay.net/api/token/', [
                "username" => "qLHUGGyAHs1yW_HeFD5CuftwNpmeYGObw1Jc-dW6gVgCvsFlx2xEhdkYS7kHv0kwHQ_YFU5UWXO1C8JPBmK-9g",
                "password" => "izvT9cWcxt_RabyortPJo0B-OkPBHjyYJMkjJy2ID1E41IkVV40dwvVv0skDXQlr2tEOb7sBKhbcDChsCZSWpQ"
            ]);

            $token = $auth->json()['token'] ?? null;

            if (!$token) {
                Log::error("Échec authentification CamPay : " . $auth->body());
                return null;
            }

            // 3. Appel de la création du lien de paiement avec le token et l'URL d'échec
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $token,
                'Content-Type'  => 'application/json',
            ])->post('https://demo.campay.net/api/get_payment_link/', [
                "amount"               => (string) round($montant),
                "currency"             => "XAF",
                "description"          => $description,
                "external_reference"   => $referenceExterne,
                "from"                 => $phone,
                "redirect_url"         => route('thank-you'),
                "failure_redirect_url" => route('thank-you'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['link'] ?? null;
            } else {
                Log::error("Erreur API CamPay [Status {$response->status()}] : " . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Exception CamPay Connection : " . $e->getMessage());
            return null;
        }
    }

    public function index(Request $request)
    {

        return view('front.commandes.thankyou');
    }
}
