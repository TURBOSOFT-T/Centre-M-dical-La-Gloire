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

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order=$order;
    }


    public function build()
    {
        return $this->view('Mail.order-mail')
            ->subject( 'NOUVELLE COMMANDE')
           ->from("no_reply@swootbio.com", config('app.name'));
    }

}
