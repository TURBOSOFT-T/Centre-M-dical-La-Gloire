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

class OrderChangeStatut extends Mailable
{
    use Queueable, SerializesModels;

    public commandes $commande;

    public function __construct($commande)
    {
        $this->commande = $commande;
    }


    public function build()
    {
        return $this->subject('Mise à jour de la  commande')->view('Mail.order-change-statut');
    }
}
