<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    // Force the model to target your new table
    protected $table = 'contact_forms'; 

    protected $fillable = [
        'name', 
        'email', 
        'subject', 
        'message', 
        'is_read'
    ];
}