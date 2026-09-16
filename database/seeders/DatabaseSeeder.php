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

        'sous_category_view',
        'sous_category_add',
        'sous_category_edit',
        'sous_category_delete',

        'famille_view',
        'famille_add',
        'famille_edit',
        'famille_delete',

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
        'sposor_add',
        'sposor_edit',
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


    public function run(): void
    {

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }





        // Créer un administrateur directement après la création de la table
        $user = new User();
        $user->nom = ' SWOOT BIO';
        $user->prenom = 'SWOOT BIO';
        $user->email = 'admin@gmail.com';
        $user->role = "admin";
        $user->adresse = '123 rue de la paix';
        $user->phone = '690884281';
        $user->code_postal = '75000';
        $user->password = Hash::make('690884281');
        $user->save();


        //creer un profil developpers
        $dev = new User();
        $dev->nom = "Client";
        $dev->prenom = 'Client';
        $dev->email = 'client@gmail.com';
        $dev->role = "client";
        $dev->adresse = '123 rue du code';
        $dev->phone = '0612345678';
        $dev->code_postal = '75000';
        $dev->password = Hash::make('123456789');
        $dev->save();


        $permissions = Permission::pluck('id', 'id')->all();

        $role = Role::create(['name' => 'admin']);
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);


        $role2 = Role::create(['name' => 'developper']);
        $dev->assignRole([$role2->id]);
        $role2->syncPermissions($permissions);


        $role = Role::create(['name' => 'personnel']);


        $cat = new config();
        $cat->frais = '0';
        $cat->description = 'Bienvenue à SWOOT BIO. Toute l\’équipe de SWOOT BIO vous souhaite la bienvenue dans notre univers.';
        $cat->telephone = '690884281';
        $cat->email = 'swootbio@gmail.com';
        $cat->addresse = 'Carrefour Orly ,Rue des Pavés Makepe BM, Douala, Cameroon, 8954';

        $cat->save();
    }
}
