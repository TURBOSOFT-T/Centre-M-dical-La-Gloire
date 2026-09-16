<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\commandes;

class FirstOrder extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user=$user;
    }


    public function build()
    {
        return $this->view('Mail.inscription')
            ->subject( 'NOUVELLE INSCRIPTION')
          ->from("no_reply@swootbio.com", config('app.name'));
    }

}
