<?php

namespace App\Http\Controllers; // <--- Doit être exactement ceci

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function update(Request $request, $id)
    {
        // 1. Validation des données
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:shops,email,' . $id,
            'phone' => 'required|string|max:20',
        ]);

        // 2. Recherche de la boutique
        $shop = Shop::findOrFail($id);

        // 3. Mise à jour
        $shop->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            // Ajoutez ici les autres champs nécessaires pour TODJOM MARKET
        ]);

        // 4. Redirection avec message de succès
        return redirect()->back()->with('success', 'Boutique mise à jour avec succès.');
    }

    public function destroy($id)
    {
        // 1. Rechercher la boutique
        $shop = Shop::findOrFail($id);

        // 2. Supprimer la boutique
        $shop->delete();

        // 3. Redirection avec un message de succès
        return redirect()->back()->with('success', 'La boutique a été supprimée avec succès de SWOOT BIO.');
    }
}