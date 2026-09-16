<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'frais',
        'ville',
       
 
     ];



     public function commandes(){
         return $this->hasMany(commandes::class, 'tranport_id', 'id');
     }

     public function commandes1()
{
    return $this->hasMany(commandes::class);
}
}
