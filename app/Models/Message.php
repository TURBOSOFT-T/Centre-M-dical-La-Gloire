<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
class Message extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'contenu', 'expediteur'];
   

    protected $listeners = [
    'refreshChat' => '$refresh'
];

public function scopeSessionActuelle($query)
{
    return $query->where('session_id', session()->getId());
}



 
}
