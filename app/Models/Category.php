<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description'
    ];



    public function produits()
    {
        return $this->hasMany(produits::class, 'category_id', 'id');
    }
    public function sous_categories()
    {
        return $this->hasMany(Sous_category::class, 'categorie_id', 'id');
    }
}
