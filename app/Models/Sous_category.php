<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sous_category extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'categorie_id'];


    public function categories()
    {
        return $this->belongsTo(Category::class,  'categorie_id', 'id');
    }

    public function produits(){
        return $this->hasMany(produits::class,'sous_categorie_id');
    }



      
    public function productCount()
    {
        return $this->produits()->count();
    }
    
}
