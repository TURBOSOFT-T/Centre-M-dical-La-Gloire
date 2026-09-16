<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class config extends Model
{
    use HasFactory;
    protected $table = 'configs';

    protected $fillable = [
        'logo',
        'logoHeader',
        'telephone',
        'slogan',
        'addresse',
        'email',
        'description',
        'frais',
        'icon',
        'logofooter',
        
        'satisfaction',
        'icone_satisfaction',
        'des_satisfaction',

        'annee',
        'icone_annee',
        'des_annee',

        'prix',
        'icone_prix',
        'des_prix',

        'titre_apropos',
        'des_apropos',
        'image_apropos',

        'titre_apropos1',
        'des_apropos1',
        'image_apropos1',
        
        'titre_apropos2',
        'des_apropos2',
        'image_apropos2',

        'image_contact',
        'image_shop',
        'image_about',
        'image_login',
        'image_register',

        'titre_annee',
        'titre_prix',
        'titre_satisfaction',
        
    ];
}
