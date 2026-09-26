<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\{User, config, Marque, Service, Category};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    private $permissions = [
        'dashboard',
        'clients_view',
        'clients_delete',

        'category_view',
        'category_add',
        'category_edit',
        'category_delete',

        'marque_view',
        'marque_add',
        'marque_edit',
        'marque_delete',

        'consultation_view',
        'consultation_add',
        'consultation_edit',
        'consultation_dlete',

        'boutique_view',
        'boutique_add',
        'boutique_edit',
        'boutique_delete',

        'visiteur_view',
        'visiteur_add',
        'visiteur_edit',
        'visiteur_delete',  

        'assurance_view',
        'assurance_add',
        'assurance_edit',   
        'assurance_delete',

        'patient_view',
        'patient_add',
        'patient_edit',
        'patient_delete',

        'rendez-vous_view',
        'rendez-vous_add',
        'rendez-vous_edit',
        'rendez-vous_delete',

        'rapport_view',
        'rapport_add',
        'rapport_edit',
        'rapport_delete',





      'dossier_medical_view',
        'dossier_medical_add',
        'dossier_medical_edit',
        'dossier_medical_delete',

        'examen_view',
        'examen_add',
        'examen_edit',
        'examen_delete',

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

    public function run(): void
    {
        // Création des permissions
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Administrateur Général - Centre Médical La Gloire
        $user = new User();
        $user->nom = 'Centre Médical';
        $user->prenom = 'La Gloire';
        $user->email = 'Centremedicallagloire56@gmail.com';
        $user->role = "admin";
        $user->adresse = 'Douala, Cameroun';
        $user->phone = '682840743';
        $user->code_postal = '00237';
        $user->password = Hash::make('682840743');
        $user->save();

        // Profil Client / Patient
        $dev = new User();
        $dev->nom = "Patient";
        $dev->prenom = 'Test';
        $dev->email = 'client@centremedicallagloire.com';
        $dev->role = "accueil";
        $dev->adresse = 'Douala, Cameroun';
        $dev->phone = '682840744';
        $dev->code_postal = '00237';
        $dev->password = Hash::make('682840744');
        $dev->save();

        // Attribution des rôles et permissions
        $permissions = Permission::pluck('id', 'id')->all();

        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions($permissions);
        $user->assignRole([$roleAdmin->id]);

        $roleDev = Role::firstOrCreate(['name' => 'developper']);
        $roleDev->syncPermissions($permissions);
        $dev->assignRole([$roleDev->id]);

        Role::firstOrCreate(['name' => 'personnel']);

        // Configuration Générale de l'Établissement    
        $cat = new config();

        $cat->description = 'Bienvenue au Centre Médical La Gloire. Une structure de santé de référence dédiée à votre bien-être et à des soins de qualité.';
        $cat->telephone = '682840743';
        $cat->email = 'Centremedicallagloire56@gmail.com';
        $cat->addresse = 'Douala, Cameroun';
        $cat->save();
    }
}
