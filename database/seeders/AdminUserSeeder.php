<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\{User, config, Marque, Service, Category};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\register as MailRegister;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Récupérer ou créer le rôle admin en précisant explicitement le guard 'web'
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );

        // 2. Récupérer TOUTES les permissions du guard 'web'
        $allPermissions = Permission::where('guard_name', 'web')->get();

        // 3. Synchroniser les permissions avec le rôle
        $adminRole->syncPermissions($allPermissions);

        // 4. Créer ou mettre à jour le premier utilisateur Admin
        $user1 = User::updateOrCreate(
            ['email' => 'tuemothomas@gmail.com'], // Critère de recherche unique
            [
                'nom' => 'Tuemo',
                'prenom' => 'Thomas',
                'role' => 'admin',
                'adresse' => 'Douala, Cameroun',
                'phone' => '672959424',
                'code_postal' => '00237',
                'password' => Hash::make('672959424'),
            ]
        );

        // 5. Créer ou mettre à jour le second utilisateur Admin
        $user2 = User::updateOrCreate(
            ['email' => 'thomastuemo@gmail.com'], // Critère de recherche unique
            [
                'nom' => 'Tuemo',
                'prenom' => 'Thomas',
                'role' => 'admin',
                'adresse' => 'Douala, Cameroun',
                'phone' => '672958053',
                'code_postal' => '00237',
                'password' => Hash::make('672958053'),
            ]
        );
    
        // 6. Leur assigner le rôle Spatie
        $user1->syncRoles([$adminRole]);
        $user2->syncRoles([$adminRole]);

        

        $this->command->info("L'utilisateur {$user1->email} a été créé et possède toutes les permissions !");
        $this->command->info("L'utilisateur {$user2->email} a été créé et possède toutes les permissions !");
    }

    //////////////Executer    la commande   php artisan db:seed --class=AdminUserSeeder
}