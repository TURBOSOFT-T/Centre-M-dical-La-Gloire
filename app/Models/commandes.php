<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class commandes extends Model
{
    use HasFactory;
    protected $table = 'commandes';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'adresse',
        'note',
        'mode',
        'avatar',
        'coupon',

        "phone",
        'phone_paiement',

        "pays",
        "gouvernorat",
        "frais",
        'password',
        'user_id',
        'reference',
        'type_commande',
        'commercial_id',
        'caisse_id',
        'shop_id',
        'table_id',
        'transport_id',
        'ontant_points',
        'buy_with_point',
        'option_payement',
        'solde_utilise',
        'points_utilise',
        'montant_total',
        'client_id'


    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id')->withDefault();
    }
    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id', 'id');
    }

    public function contenus()
    {
        return $this->hasMany(contenu_commande::class, 'id_commande');
    }

    public function montant()
    {
        $total = $this->frais;
        foreach ($this->contenus as $contenu) {
            if ($contenu->produit->free_shipping == true) {
                $total += $contenu->prix_unitaire * $contenu->quantite - $this->frais;
            } else {
                $total += $contenu->prix_unitaire * $contenu->quantite;
            }
        }
        return $total ?? 0;
    }

    /*   public function client(){
        return $this->belongsTo(clients::class, 'phone','phone');
    }
 */
    public function modifiable()
    {
        if ($this->statut === 'retournée' || $this->statut === 'payée' || $this->statut === 'livrée') {
            return false;
        } else {
            return true;
        }
    }

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id')->withDefault();
    }

    public function caissier()
    {
        return $this->belongsTo(User::class, 'caisse_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function totalPoints()
    {
        $points = 0;

        foreach ($this->contenus as $contenus) {
            if ($contenus->produit) {
                $points += $contenus->quantite * $contenus->produit->points;
            }
        }
        return $points;
    }



    public function client()
    {
        return $this->belongsTo(clients::class, 'client_id');
    }
}
