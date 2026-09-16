<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'message',
        'statut',
        'type',
        'url',
        'notifiable',
        'data',
        'read_at',
     
       
    ];
}
