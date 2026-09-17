<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SellerSeeder extends Seeder
{
    /**
     * Permissions spécifiques au guard 'seller'
     */
    private $permissionsSeller = [
        'dashboard',
        'clients_view',
        'clients_delete',
        'category_view',
        'category_add',
        'category_edit',
        'category_delete',
        'trasport_view',
        'trasport_add',
        'trasport_edit',
        'trasport_delete',
        'code_promotion_view',
        'code_promotion_add',
        'code_promotion_edit',
        'code_promotion_delete',
        'compte_view',
        'compte_add',
        'compte_edit',
        'compte_delete',
        'ajouter_solde_view',
        'ajouter_solde_add',
        'ajouter_solde_edit',
        'ajouter_solde_delete',
        'marque_view',
        'marque_add',
        'marque_edit',
        'marque_delete',
        'sponsor_view',
        'sposor_edit',
        'sposor_add',
        'sponsor_delete',
        'service_view',
        'service_add',
        'service_edit',
        'service_delete',
        'product_view',
        'product_add',
        'product_edit',
        'product_delete',
        'coupon_view',
        'coupon_add',
        'coupon_edit',
        'coupon_delete',
        'testimonial_view',
        'testimonial_add',
        'testimonial_edit',
        'testimonial_delete',
        'table_view',
        'table_add',
        'table_edit',
        'table_delete',
        'price_view',
        'order_view',
        'order_add',
        'order_edit',
        'order_delete',
        'my_order_view',
        'live_order_view',
        'live_order_add',
        'live_order_edit',
        'live_order_delete',
        'setting_view',
        'message_view',
        'gestion_stock'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer ou récupérer le rôle pour le guard 'seller'
        $roleSeller = Role::findOrCreate('seller', 'seller');

        // 2. Créer et synchroniser les permissions du guard 'seller'
        foreach ($this->permissionsSeller as $permissionName) {
            $permission = Permission::findOrCreate($permissionName, 'seller');
            // Associe directement la permission au rôle
            $roleSeller->givePermissionTo($permission);
        }

        // 3. Créer un compte de test partenaire/prestataire pour le Centre Médical La Gloire
        $shop = Shop::updateOrCreate(
            ['email' => 'partenaire@centremedicallagloire.com'],
            [
                'name' => 'Centre Médical La Gloire - Unité Principale',
                'description' => 'Espace de gestion des soins et prestations de santé',
                'phone' => '682840743',
                'adresse' => 'Douala, Cameroun',
                'role' => 'seller',
                'active' => true,
                'statut' => 'disponible',
                'password' => Hash::make('123456789'),
            ]
        );

        // 4. Assigner le rôle au vendeur/prestataire connecté sur le guard 'seller'
        $shop->assignRole($roleSeller);
    }
}