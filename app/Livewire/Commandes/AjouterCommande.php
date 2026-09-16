<?php

namespace App\Livewire\Commandes;

use App\Http\Traits\ListGouvernorats;
use App\Models\clients;
use App\Models\commandes;
use App\Models\config;
use App\Models\contenu_commande;
use App\Models\packs;
use App\Models\produits;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class AjouterCommande extends Component
{
    public $key, $nom, $pays, $prenom, $frais, $adresse, $gouvernorat, $phone, $recherche, $clients = [];
    public $quantites = [];
    public $produits = [];
    public $panier, $gouvernoratsTunisie;

    // Le shop_id sélectionné en haut de la page
    public $shop_id;
    public $stocksDisponibles = [];

    use ListGouvernorats;


    public function updateKey($value)
    {
        $this->key = $value;
        $this->resetPage();
    }

    // Réinitialise les résultats de recherche si l'utilisateur change de magasin en cours de route
    public function updatedShopId()
    {
        $this->key = '';
        $this->produits = [];
    }

    public function updatedRecherche($recherche)
    {
        if (strlen($recherche) > 0) {
            $this->clients = clients::where('nom', 'like', '%' . $recherche . '%')
                ->orWhere('prenom', 'like', '%' . $recherche . '%')
                ->orWhere('phone', 'like', '%' . $recherche . '%')
                ->take(10)
                ->get();
        } else {
            $this->clients = [];
        }
    }

    public function import($client)
    {
        $this->nom = $client["nom"];
        $this->prenom = $client["prenom"];
        $this->adresse = $client["adresse"];
        $this->phone = $client["phone"];

        $this->recherche = "";
        $this->clients = [];

        session()->flash("message", "Client Importé avec succés");
    }

    public function ajouterProduit($produitId, $type, $reference)
    {
        if (!$this->shop_id) {
            session()->flash('error', "Veuillez d'abord sélectionner un magasin.");
            return;
        }

        $article = produits::find($produitId);
        if (!$article) {
            session()->flash('error', "Le produit n'existe pas.");
            return;
        }

        // 1. Récupérer le stock spécifique au magasin
        $shopRecord = $article->shops()->where('shop_id', $this->shop_id)->first();
        $stockDispo = $shopRecord ? $shopRecord->pivot->stock_particulier : 0;

        $quantiteDemandee = max(1, $this->quantites[$produitId] ?? 1);
        $cartData = Session::get('panier', []);

        // 2. Vérifier la quantité déjà présente dans le panier actuel
        $quantiteDansPanier = 0;
        $indexProduit = null;
        foreach ($cartData as $index => $item) {
            if ($item['id'] == $produitId && $item['type'] == $type && $item['reference'] == $reference) {
                $quantiteDansPanier = $item['quantite'];
                $indexProduit = $index;
                break;
            }
        }

        $totalNecessaire = $quantiteDansPanier + $quantiteDemandee;

        // 3. Validation croisée : Total vs Stock magasin
        if ($totalNecessaire > $stockDispo) {
            session()->flash('error', "Stock de cette boutique est  insuffisant  (Dispo: $stockDispo).");
            return;
        }

        // 4. Mise à jour du panier
        if ($indexProduit !== null) {
            $cartData[$indexProduit]['quantite'] = $totalNecessaire;
        } else {
            $cartData[] = [
                "id" => $article->id,
                "quantite" => $quantiteDemandee,
                "type" => $type,
                "nom" => $article->nom,
                "prix" => $article->getPrice(), // Utilisation de votre méthode getPrice()
                "reference" => $reference,
            ];
        }

        $this->quantites[$produitId] = 1;
        Session::put('panier', $cartData);
    }

    // Dans App\Models\produits.php

    public function getStockInShopAttribute($shopId)
    {
        $shop = $this->shops()->where('shop_id', $shopId)->first();
        return $shop ? $shop->pivot->stock_particulier : 0;
    }

    public function render()
{
    $user = auth()->user();

    // 1. Les admins voient tout, les autres voient uniquement leurs boutiques rattachées
    if ($user->hasRole('admin') || $user->role === 'admin') {
        $shops = Shop::all();
    } else {
        $shops = $user->shops;
    }

    // Sélection de la boutique active
    $activeShopId = $this->shop_id ?: $shops->first()?->id;

    $paniers = session()->get('panier', []);

    if (!is_null($this->key) && !empty($activeShopId)) {
        // Sécurité : l'utilisateur a-t-il le droit d'interroger ce shop ?
        $canAccessShop = ($user->hasRole('admin') || $user->role === 'admin') 
            ? true 
            : $shops->contains('id', $activeShopId);

        if ($canAccessShop) {
            $produits = produits::where('nom', 'like', '%' . $this->key . '%')
                ->whereHas('shops', function ($query) use ($activeShopId) {
                    $query->where('shop_id', $activeShopId)
                          ->where('stock_particulier', '>', 0);
                })
                ->take(2)
                ->get();

            $result = [];
            $this->stocksDisponibles = [];

            foreach ($produits as $produit) {
                $shopRecord = $produit->shops()->where('shop_id', $activeShopId)->first();

                $this->stocksDisponibles[$produit->id] = $shopRecord ? $shopRecord->pivot->stock_particulier : 0;

                $result[] = [
                    'id'        => $produit->id,
                    'nom'       => $produit->nom,
                    'prix'      => $produit->prix,
                    'reference' => $produit->reference,
                    'type'      => 'produit'
                ];
            }

            $this->produits = $result;
        } else {
            $this->produits = [];
            $this->stocksDisponibles = [];
        }
    } else {
        $this->produits = [];
        $this->stocksDisponibles = [];
    }

    $this->gouvernoratsTunisie = $this->getListGouvernorat();

    return view('livewire.commandes.ajouter-commande', compact('paniers', 'shops'));
}

    public function delete_from_session($produitId)
    {
        $cartData = Session::get('panier', []);

        if (!is_array($cartData)) {
            $cartData = [];
        }

        foreach ($cartData as $index => $item) {
            if ($item['id'] == $produitId) {
                unset($cartData[$index]);
                break;
            }
        }

        $cartData = array_values($cartData);
        Session::put('panier', $cartData);
    }
public function order()
    {
        $this->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:150',
            'phone' => 'required|string|max:100',
            'pays' => 'nullable|string|max:100',
            'gouvernorat' => 'nullable|string|max:12',
            'frais' => 'nullable',
            'shop_id' => 'required',
        ]);

        $user_id = Auth::check() ? Auth::id() : null;
        $caisse_id = Auth::id();
        $final_shop_id = $this->shop_id;

        // Gestion du client
        $client = clients::updateOrCreate(
            ['phone' => $this->phone],
            ['nom' => $this->nom]
        );

        $reference = 'SWB-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $panier = session()->get('panier', []);

        if ($panier) {
            $config = config::first();
            
            // 1. Calculer le montant total du panier au préalable
            $montantTotal = 0;
            foreach ($panier as $item) {
                if ($item["type"] == "produit") {
                    $article = produits::find($item["id"]);
                    if ($article) {
                        $montantTotal += $article->getPrice() * intval($item["quantite"]);
                    }
                }
            }

            // Ajouter les frais de livraison si activés
            $fraisMontant = $this->frais && $config ? $config->frais : 0;
            $montantTotal += $fraisMontant;

            // 2. Création de la commande avec le montant total
            $commande = new commandes();
            $commande->nom = $this->nom;
            $commande->user_id = $user_id;
            $commande->client_id = $client->id;
            $commande->reference = $reference;
            $commande->phone = $this->phone;
            $commande->caisse_id = $caisse_id;
            $commande->shop_id = $final_shop_id;
            $commande->frais = $fraisMontant > 0 ? $config->frais : null;
            $commande->montant_total = $montantTotal; // Assurez-vous que cette colonne existe dans votre table 'commandes'
            
            if ($commande->save()) {
                foreach ($panier as $item) {
                    $type = $item["type"];
                    $quantite = intval($item["quantite"]);

                    if ($type == "produit") {
                        $article = produits::find($item["id"]);
                        if ($article) {
                            $contenu = new contenu_commande();
                            $contenu->id_commande = $commande->id;
                            $contenu->id_produit = $article->id;
                            $contenu->shop_id = $final_shop_id; 
                            $contenu->quantite = $quantite;
                            $contenu->type = $type;
                            $contenu->prix_unitaire = $article->getPrice();
                            $contenu->benefice = ($article->getPrice() - $article->prix_achat) * $quantite;
                            $contenu->save();

                            // Déduction du stock
                            $article->diminuer_stock($quantite, $final_shop_id);
                        }
                    }
                }

                session()->forget('panier');
                return redirect()->route('details_commande', ["id" => $commande->id])->with("success", "Votre commande a été enregistrée");
            } else {
                session()->flash('warning', 'Échec de la création de la commande.');
            }
        } else {
            session()->flash('warning', 'Votre panier est vide.');
        }
    }
    public function order2()
    {
        $this->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:150',
            'phone' => 'required|string|max:100',
            'pays' => 'nullable|string|max:100',
            'gouvernorat' => 'nullable|string|max:12',
            'frais' => 'nullable',
            'shop_id' => 'required',
        ]);

        $user_id = Auth::check() ? Auth::id() : null;
        $caisse_id = Auth::id();
        $final_shop_id = $this->shop_id;

        $client = clients::where('phone', $this->phone)->first();

        if ($client) {
            $client->update(
                ['phone' => $this->phone],
                ['nom' => $this->nom]
            );
        } else {
            $client = clients::create([
                'nom' => $this->nom,
                'phone' => $this->phone,
            ]);
        }

        $reference = 'SWB-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $panier = session()->get('panier', []);

        if ($panier) {
            $config = config::first();
            $commande = new commandes();
            $commande->nom = $this->nom;
            $commande->user_id = $user_id;
            $commande->client_id = $client->id;
            $commande->reference = $reference;
            $commande->phone = $this->phone;
            $commande->caisse_id = $caisse_id;
            $commande->shop_id = $final_shop_id;
            $commande->frais = $this->frais ? $config->frais : null;

            if ($commande->save()) {
                foreach ($panier as $item) {
                    $type = $item["type"];
                    $quantite = intval($item["quantite"]);

                    if ($type == "produit") {
                        $article = produits::find($item["id"]);
                        if ($article) {
                            $contenu = new contenu_commande();
                            $contenu->id_commande = $commande->id;
                            $contenu->id_produit = $article->id;
                            $contenu->shop_id = $final_shop_id; // <-- Enregistrement du shop associé sur chaque ligne du contenu
                            $contenu->quantite = $quantite;
                            $contenu->type = $type;
                            $contenu->prix_unitaire = $article->getPrice();
                            $contenu->benefice = ($article->getPrice() - $article->prix_achat) * $quantite;
                            $contenu->save();

                            // On passe maintenant explicitement le shop pour déduire le stock au bon endroit
                            $article->diminuer_stock($quantite, $final_shop_id);
                        }
                    }
                }

                session()->forget('panier');
                return redirect()->route('details_commande', ["id" => $commande->id])->with("success", "Votre commande a été enregistrée");
            } else {
                session()->flash('warning', 'Échec de la création de la commande.');
            }
        } else {
            session()->flash('warning', 'Votre panier est vide.');
        }
    }
}
