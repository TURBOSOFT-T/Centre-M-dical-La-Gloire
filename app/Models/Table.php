<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

     protected $fillable = [
    
    'nom',
    'description',
  
    'photo',
   
    ];


      public function commandes1(){
        return $this->hasMany(contenu_commande::class,'table_id','id');
    }
     public function commandes(){
        return $this->hasMany(commandes::class,'table_id','id');
    }
}
