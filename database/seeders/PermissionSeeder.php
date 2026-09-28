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


        'labo_view',
        'labo_add',
        'labo_edit',
        'labo_delete',

        'hospitalisation_view',
        'hospitalisation_add',
        'hospitalisation_edit',
        'hospitalisation_delete',

        'consultation_delete',

        'consultation_edit_champ',
        'consultation_add_comment',
        'consultation_add_evolution',
        'consultation_add_exam',
        'consultation_add_bilan',
          'consultation_pay',
        'consultation_notifications',
        


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
