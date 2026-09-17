<?php

use App\Http\Controllers\Back\{
    AdminController,
    StatistiqueController,
};



use App\Http\Controllers\BannersController;


use App\Http\Controllers\ForgotPasswordController;

use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\{

    ContactController,
    panier_client,
    CommandeController,
    LocaleController,
    MyAccountController,
    favoris_client,
    HomeController,
    TestimonialController
};
use App\Http\Controllers\ShopController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('/locale', [LocaleController::class, 'change'])->name("locale.change");

Route::get('contact', [ContactController::class, 'contact'])->name("contact");
Route::get('about', [ContactController::class, 'about'])->name("about");
/////temoignages
Route::resource('testimonial', TestimonialController::class);



Route::resource('contacts', ContactController::class, ['only' => ['create', 'store']]);
Route::get('forgot_password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forgot_password');
Route::get('/confirmation', [HomeController::class, 'confirmation'])->name('confirmation');
Route::get('/logout', [HomeController::class, 'logout']);
Route::get('/verify', [HomeController::class, 'verify'])->name('2fa.index');
//Route::get('/verify',[HomeController::class, 'verify'])->name('register2fa.index');
Route::get('/registerverify', [HomeController::class, 'registerverify'])->name('register2fa.index');



//Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('login', [HomeController::class, 'home'])->name('login');

Route::get('/print/commande/{id}', [HomeController::class, 'print_commande'])->name('print_commande');
Route::get('/commande_print/{id}', [HomeController::class, 'print'])->name('commande_print');
Route::get('/print_bordereau/{ids}', [HomeController::class, 'print_bordereau'])->name('print_bordereau');




Route::get('/details-produits/{id}/{slug}', [HomeController::class, 'details'])->name('details-produits');


///gestion boutique
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::post('/shop', [HomeController::class, 'shop']);
Route::get('/shop/reset', [HomeController::class, 'resetShop'])->name('shop.reset');
Route::post('/commande-whatsapp', [HomeController::class, 'orderWhatsapp'])->name('order.whatsapp');

//gestion du panier
Route::get('cart', [panier_client::class, 'cart'])->name('cart');
Route::post('/client/ajouter_au_panier', [panier_client::class, 'add']);
Route::get('/client/count_panier', [panier_client::class, 'count_panier']);
Route::get('/client/mon_panier', [panier_client::class, 'contenu_mon_panier']);
Route::get('/client/delete_produit_au_panier', [panier_client::class, 'delete_produit']);
// Vérifiez si cette route existe dans votre web.php
Route::post('/client/delete_produit_au_panier', [App\Http\Controllers\Front\panier_client::class, 'delete_produit']);


Route::get('/commander', [CommandeController::class, 'commander'])->name('commander');
Route::post('/order', [CommandeController::class, 'confirmOrder'])->name('order.confirm');
Route::get('/thank-you', [CommandeController::class, 'index'])->name('thank-you');


//Route::get('cart', [CommandeController::class, 'cart'])->name('cart');
Route::delete('/cart/clear', [CommandeController::class, 'clear'])->name('cart.clear');

Route::post('/savecoupon', [CouponController::class, 'savecoupon'])->name('savecoupon');
Route::post('/apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon');



// Utilisateur authentifié
Route::middleware('auth')->group(function () {

    //gestion des favoris
    Route::post('/client/ajouter_favoris', [favoris_client::class, 'add']);
    Route::get('/favories', [MyAccountController::class, 'favories'])->name('favories');

    ///Mon compte
    Route::get('/comptes', [MyAccountController::class, 'comptes'])->name('comptes');
    Route::get('/account', [MyAccountController::class, 'account'])->name('account');

    ///Mon profil
    Route::get('/profile', [MyAccountController::class, 'profile'])->name('profile');
});


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('dashboard');
    Route::post('/dashboard/filtre', [AdminController::class, 'dashboard'])
        ->name('filtre-dashboard');

    ////////////Les categories/////////////////////////////////////////////////////
    Route::get('/admin/categories', [AdminController::class, 'categories'])
        ->name('categories')
        ->middleware('permission:category_view');
    Route::get('/admin/category/add', [AdminController::class, 'category_add'])
        ->name('category.add')
        ->middleware('permission:category_add');
    Route::get('/admin/category/{id}/update', [AdminController::class, 'categories_update'])
        ->name('categories.update')
        ->middleware('permission:category_edit');

    ///////////////////Transports/////////////////
    Route::get('/admin/transports', [AdminController::class, 'transports'])
        ->name('transports');
    Route::get('/admin/transport/add', [AdminController::class, 'transport_add']);


    Route::get('/admin/support', [AdminController::class, 'support'])->name('support');
    Route::get('/admin/caissiers', [AdminController::class, 'caissiers'])
        ->name('caissiers');




    ///////////////////Les coupons////////////////////////////////////////////////
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])
        ->name('coupons');

    Route::post('/savecoupon', [CouponController::class, 'savecoupon'])->name('savecoupon');
    Route::get('/updatecoupon/{id}', [CouponController::class, 'updatecoupon'])->name('updatecoupon');
    Route::delete('/deletecoupon/{id}', [CouponController::class, 'destroy'])->name('coupon.destroy');
    Route::get('/putcoupon/{id}', [CouponController::class, 'putcoupon'])->name('putcoupon');

    Route::resource('/coupons', CouponController::class);


    /////////Testimonials///////////////////
    Route::get('/admin/testimonials', [AdminController::class, 'testimonials'])
        ->name('testimonials');
    Route::get('/admin/testimonial/{id}/delete', [AdminController::class, 'testimonial_delete']);
    Route::resource('testimonials', TestimonialController::class);
    Route::get('temoignages/{id}/disapprove', [TestimonialController::class, 'disapprove'])->name('temoignages.disapprove');
    Route::get('temoignages/{id}/approve', [TestimonialController::class, 'approve'])->name('temoignages.approve');


    Route::get('/admin/coupon/add', [AdminController::class, 'coupon_add'])
        ->name('coupon.add');

    ///////////////////Les produits////////////////////////////


    /////////////////////////Les marques//////////////////////////////////////
    Route::get('/admin/marques', [AdminController::class, 'marques'])
        ->name('marques');




    ///////////////////les  produits////////////////////////////////////////////////
    Route::prefix('admin')->group(function () {
        Route::get('/produits', [AdminController::class, 'produits'])
            ->name('produits')
            ->middleware('permission:product_view');

        Route::get('/corbeille', [AdminController::class, 'corbeille'])->name('corbeille');
        Route::get('/produit/{id}/update', [AdminController::class, 'produits_update'])
            ->name('produits.update')
            ->middleware('permission:product_edit');

        Route::get('/produit/{id}/historique', [AdminController::class, 'historique'])
            ->name('produits.historique')
            ->middleware('role:admin');

        Route::get('/produit/add', [AdminController::class, 'produit_add'])
            ->name('produit.add')
            ->middleware('permission:product_add');

        Route::get('/commandes', [AdminController::class, 'commandes'])
            ->name('commandes')
            ->middleware('permission:order_view');

        Route::get('/commandes_en_lives', [AdminController::class, 'commandes_en_lives'])
            ->name('commandes_en_lives')
            ->middleware('permission:order_view');
        Route::get('/commandes_commercial', [AdminController::class, 'commandes_commercial'])
            ->name('commandes_commercial')
            ->middleware('permission:order_view');
        Route::get('/parametres', [AdminController::class, 'parametres'])
            ->name('parametres');

        Route::get('/personnels', [AdminController::class, 'personnels'])
            ->name('personnels')
            ->middleware('role:admin');
        Route::post('/admin/personnels/{id}/update-role', [AdminController::class, 'updateRole'])
            ->name('admin.personnels.updateRole')->middleware('role:admin');
        Route::get('/promotions', [AdminController::class, 'promotions'])
            ->name('promotions');
        Route::get('/promotions/{id}', [AdminController::class, 'promotions'])
            ->name('promotions_produit');
        Route::get('/commande/{id}', [AdminController::class, 'details_commande'])
            ->name('details_commande');

        Route::post('/produits/{id}/ajouter-stock', [AdminController::class, 'ajouterStock'])->name('produits.ajouterStock');



        //////////////////////SHOPS//////////////////////////////////

        Route::get('/shops', [AdminController::class, 'shops'])
            ->name('shops')
            ->middleware('role:admin');
        Route::post('/admin/shops/{id}/update-role', [AdminController::class, 'updateRoleShop'])
            ->name('admin.shops.updateRoleShop')->middleware('role:admin');


        Route::post('/admin/update-shop-permissions', [AdminController::class, 'update_permission'])
            ->name('update-shop-permissions');

        Route::get('/admin/shop/toggle-status/{id}', [AdminController::class, 'toggleStatus'])->name('shops.toggle');

        Route::get('/shop/edit/{id}', [ShopController::class, 'edit'])->name('shop.edit');
        Route::post('/admin/shop/update/{id}', [ShopController::class, 'update'])->name('shop.update');
        // Route::get('/admin/shop/delete/{id}', [ShopController::class, 'destroy'])->name('shop.delete');
        Route::get('/admin/shop/delete/{id}', [App\Http\Controllers\ShopController::class, 'destroy'])->name('shop.delete');
    });






    Route::get('clients', [AdminController::class, 'clients'])
        ->name('clients')
        ->middleware('permission:clients_view');
    Route::get('/admin/export/clients', [AdminController::class, 'export_clients'])
        ->name('export_clients')
        ->middleware('permission:clients_view');
    Route::get('comptes', [AdminController::class, 'comptes'])
        ->name('comptes');

    Route::get('contact-admin', [AdminController::class, 'contact_admin'])
        ->name('contact-admin')
        ->middleware('permission:setting_view');
    Route::get('/admin/get_live_notifications', [AdminController::class, 'live_notifications'])
        ->name('live_notifications');

    Route::post('/update-config', [AdminController::class, 'update_config'])
        ->name('update-config');

    Route::get('admin/new_commande', [AdminController::class, 'new_commande'])
        ->name('new_commande')
        ->middleware('permission:order_add');

    Route::post('admin/add_note', [AdminController::class, 'add_note'])
        ->name('add_note')
        ->middleware('permission:order_edit');


    Route::get('/admin/commande/{id}/edit_commande', [AdminController::class, 'edit_commande'])
        ->name('edit_commande')
        ->middleware('permission:order_edit');




    Route::get('/admin/statistiques', [StatistiqueController::class, 'index'])->name('admin.statistiques');

    Route::group(['middleware' => 'role:admin'], function () {

        Route::get('/admin/personnel/delete/{id}', [AdminController::class, 'delete_personnel'])
            ->name('delete_personnel');

        Route::get('/corbeilles', [AdminController::class, 'corbeillepersonnel'])->name('corbeilles');

        Route::post('/admin/update-personnel-permissions', [AdminController::class, 'update_permission'])
            ->name('update-personnel-permissions');

        //gestion des routes pour le forumlaire de contact
        Route::get('/admin/admin_contact_form', [AdminController::class, 'admin_contact_form'])
            ->name('admin_contact_form');

        Route::get('/admin/supprimer_messages/{id}', [AdminController::class, 'supprimer_messages'])
            ->name('supprimer_messages');




        //getion des banniers
        Route::get('/admin/banner/index', [BannersController::class, 'index'])
            ->name('banner.index');
        Route::get('/admin/banner/{id}', [BannersController::class, 'index_update'])
            ->name('banner.update');
    });
});


require __DIR__ . '/auth.php';
