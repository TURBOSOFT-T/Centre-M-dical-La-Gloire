<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

 protected $table="examens";

    protected $primaryKey="id";

    protected $fillable=[ 'nom', 'caracteristiques'];

      protected $casts = [
     
        'caracteristiques' => 'array',
    ];



public function user()
{
    return $this->belongsTo(User::class , 'user_id', 'id');
}



}
