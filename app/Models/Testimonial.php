<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Events\ModelCreated;
use Illuminate\Notifications\Notifiable;
class Testimonial extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
       'name',
       'email',
       'message',
       'user_id',
       'photo',

        'active',
      

    ];
    public $timestamps = true;


    public function user(){
        return $this->belongsTo(User::class);
    }
}
