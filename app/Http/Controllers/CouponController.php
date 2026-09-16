<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\produits;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $coupon = Coupon::orderBy('id', 'DESC')->paginate('10');
        return view('backend.coupon.index')->with('coupons', $coupon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $commercials = User::where('role', 'commercial')->get();
        return view('backend.coupon.create', compact('commercials'));
    }

    public function savecoupon(Request $request)
    {
        // 🔹 Validation stricte
        $request->validate([
            'code'          => 'required|string|max:4|unique:coupons,code', // Unique et max 4 caractères
            'type'          => 'required|in:fixed,percent',
            'value'         => 'required|numeric|min:0',
            'status'        => 'required|in:active,inactive',
            'is_commercial' => 'required|boolean',
            // Si is_commercial est vrai (1), le commercial_id devient requis
            'commercial_id' => 'required_if:is_commercial,1|nullable|exists:users,id',
            // Validation de la date d'expiration (optionnelle, doit être une date valide future ou présente)
            'expires_at'    => 'nullable|date',
        ], [
            'commercial_id.required_if' => 'Veuillez sélectionner un commercial puisque l\'option est activée.',
            'code.unique' => 'Ce code coupon existe déjà.',
            'expires_at.date' => 'La date d\'expiration doit être une date valide.',
        ]);

        // 🔹 Récupération des données nettoyées de la validation (en incluant 'expires_at')
        $data = $request->only(['code', 'type', 'value', 'status', 'is_commercial', 'commercial_id', 'expires_at']);

        // 🔹 Logique d'association du commercial
        if ($data['is_commercial'] == '1' || $data['is_commercial'] === true) {
            // Option cochée : on garde le commercial choisi dans le formulaire
            $data['is_commercial'] = true;
        } else {
            // Option décochée : pas de commercial lié, on force à null
            $data['is_commercial'] = false;
            $data['commercial_id'] = null;
        }

        // 🔹 Création du coupon
        $coupon = Coupon::create($data);

        // 🔹 Messages flash
        if ($coupon) {
            session()->flash('success', 'Coupon successfully added');
        } else {
            session()->flash('error', 'Please try again!!');
        }

        return redirect()->route('coupons');
    }

    public function updatecoupon($id)
    {
        $coupon = Coupon::find($id);

        if ($coupon) {
            // 🔹 Récupérer la liste des commerciaux pour alimenter le sélecteur dans la vue
            // Ajuste la condition selon ton système (ex: ->where('role', 'commercial'))
            $commercials = \App\Models\User::where('role', 'commercial')->get();

            return view('admin.coupons.update')
                ->with('coupon', $coupon)
                ->with('commercials', $commercials);
        } else {
            // Optionnel : Il est souvent préférable d'utiliser une redirection avec un message flash
            // pour éviter d'afficher la vue 'list' sans ses propres variables requises.
            return redirect()->route('coupons')->with('error', 'Coupon not found');
        }
    }


    
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        // 🔹 Validation stricte avec exception pour le code actuel du coupon
        $request->validate([
            'code'          => 'required|string|max:4|unique:coupons,code,' . $coupon->id,
            'type'          => 'required|in:fixed,percent',
            'value'         => 'required|numeric|min:0',
            'status'        => 'required|in:active,inactive',
            'is_commercial' => 'required|boolean',
            // Requis uniquement si l'interrupteur is_commercial est actif (1)
            'commercial_id' => 'required_if:is_commercial,1|nullable|exists:users,id',
            // Validation de la date d'expiration
            'expires_at'    => 'nullable|date',
        ], [
            'commercial_id.required_if' => 'Veuillez sélectionner un commercial puisque l\'option est activée.',
            'code.unique'               => 'Ce code coupon est déjà utilisé par un autre enregistrement.',
            'expires_at.date'           => 'La date d\'expiration doit être une date valide.',
        ]);

        // 🔹 Extraction et nettoyage des données nécessaires (avec expires_at)
        $data = $request->only(['code', 'type', 'value', 'status', 'is_commercial', 'commercial_id', 'expires_at']);

        // 🔹 Logique de nettoyage si l'interrupteur a été désactivé
        if ($data['is_commercial'] == '1' || $data['is_commercial'] === true) {
            $data['is_commercial'] = true;
        } else {
            $data['is_commercial'] = false;
            $data['commercial_id'] = null; // Nettoie l'id précédent en base de données
        }

        // 🔹 Mise à jour
        $status = $coupon->update($data);

        // 🔹 Gestion des messages de session
        if ($status) {
            $request->session()->flash('success', 'Coupon Successfully updated');
        } else {
            $request->session()->flash('error', 'Please try again!!');
        }

        return redirect()->route('coupons');
    }
  

    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            $status = $coupon->delete();
            if ($status) {
                request()->session()->flash('success', 'Coupon successfully deleted');
            } else {
                request()->session()->flash('error', 'Error, Please try again');
            }
            return redirect()->route('coupons');
        } else {
            request()->session()->flash('error', 'Coupon not found');
            return redirect()->back();
        }
    }



    public function couponStore(Request $request)
    {
        // return $request->all();
        $coupon = Coupon::where('code', $request->code)->first();
        // dd($coupon);
        if (!$coupon) {
            request()->session()->flash('error', 'Invalid coupon code, Please try again');
            return back();
        }
        if ($coupon) {
            //  $total=Cart::where('user_id',auth()->user()->id)->where('order_id',null)->sum('price');
            $paniers_session = session('cart', []);
            $paniers = [];
            $total = 0;
            foreach ($paniers_session as $session) {
                $produit = produits::find($session['id_produit']);
                if ($produit) {
                    $paniers[] = [
                        'nom' => $produit->nom,
                        'id_produit' => $produit->id,
                        'photo' => $produit->photo,
                        'quantite' => $session['quantite'],
                        'prix' => $produit->prix,
                        'total' => $session['quantite'] * $produit->prix,
                    ];
                    //   $total += $session['quantite'] * $produit->prix;

                    session()->put('coupon', [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'value' => $coupon->discount($total)
                    ]);
                }
            }


            request()->session()->flash('success', 'Coupon successfully applied');
            return redirect()->back();
        }
    }





    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request->input('code'))->first();
        if (
            !$coupon ||
            $coupon->status !== 'active' ||
            ($coupon->expires_at && \Carbon\Carbon::parse($coupon->expires_at)->isPast())
        ) {
            request()->session()->flash('error', 'Invalid, inactive or expired coupon code, Please try again');
            return back();
        }

        $paniers_session = session('cart', []);
        $total = 0;

        // 1. Calculer le total réel du panier d'abord
        foreach ($paniers_session as $shop_id => $produits_data) {
            foreach ($produits_data as $id_produit => $item) {
                $produit = produits::find($id_produit);
                if ($produit) {
                    // Utilisation de la méthode getPrice adaptée au shop_id
                    $prix = $produit->getPrice($shop_id);
                    $total += ($item['quantite'] * $prix);
                }
            }
        }

        if ($total <= 0) {
            return redirect()->back()->with('error', 'Votre panier est vide.');
        }

        // 2. Calculer la réduction
        $discount = 0;
        if ($coupon->type == 'percent') {
            $discount = ($total * $coupon->value) / 100;
        } elseif ($coupon->type == 'fixed') {
            $discount = $coupon->value;
        }

        // 3. Stocker le coupon en session
        session()->put('coupon', [
            'id'    => $coupon->id,
            'code'  => $coupon->code,
            'value' => $discount,
        ]);

        return redirect()->back()->with('success', "Coupon appliqué ! Réduction de {$discount}.");
    }
}
