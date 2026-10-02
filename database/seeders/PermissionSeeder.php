<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\{User, config, Marque, Service, labo, Shop};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    private $permissions = [


       

        'hospitalisation_stat_view',
        'produit_stat_view',
        'concultation_stat_view',
        



    ];

    public function run(): void
    {
        // Création des permissions
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }


        // 1. Créer ou récupérer le rôle pour le guard 'seller'
        $roleSeller = Role::findOrCreate('seller', 'seller');

        // 2. Créer et synchroniser les permissions du guard 'seller'
        foreach ($this->permissions as $permissionName) {
            $permission = Permission::findOrCreate($permissionName, 'seller');
            // Associe directement la permission au rôle
            $roleSeller->givePermissionTo($permission);
        }
    }
}
