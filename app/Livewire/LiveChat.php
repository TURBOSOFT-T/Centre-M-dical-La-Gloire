<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use App\Models\notifications;
use Livewire\Attributes\On; // Requis pour l'écouteur #[On] dans Livewire v3

class LiveChat extends Component
{
    public $ouvert = false; // Gère l'affichage de la fenêtre
    public $nouveauMessage = '';

    // Utilisation des propriétés pour l'auto-refresh ou écouteurs selon la version de Livewire
    protected $listeners = [
        'refreshChat' => '$refresh',
        'bip-client' => 'recevoirReponseAdmin' // Le client écoute les réponses de l'admin
    ];

    // Si vous utilisez Livewire v3, cette méthode alternative avec l'attribut #[On] fonctionne aussi :
    #[On('bip-client')]
    public function recevoirReponseAdmin()
    {
        // Cette méthode force simplement le composant du client à se rafraîchir
        // pour afficher la réponse de l'admin dès que l'événement est reçu.
    }

    public function toggleChat()
    {
        $this->ouvert = !$this->ouvert;
    }

    public function envoyer()
    {
        $this->nouveauMessage = trim($this->nouveauMessage);

        $this->validate([
            'nouveauMessage' => ['required', 'string', 'max:1000'],
        ]);

        Message::create([
            'user_id'     => auth()->id(),
            'session_id'  => session()->getId(),
            'contenu'     => $this->nouveauMessage,
            'expediteur'  => 'client',
        ]);

        notifications::create([
            'titre'   => 'Nouveau message du service client',
            'message' => 'Un client vient d\'envoyer un nouveau message.',
            'type'    => 'tchat',
        ]);

        $this->reset('nouveauMessage');

        // 1. Force le défilement vers le bas du chat actuel
        $this->dispatch('scroll-chat-bottom');

        // 2. Alerte les composants d'administration (Le dropdown de messages et le compteur de notifs)
        $this->dispatch('messageSent'); 
        $this->dispatch('notificationReceived');

        // 3. Déclenche le BIP SONORE et le TOAST SweetAlert2 chez l'administrateur
        $this->dispatch('bip-admin');
    }

    public function render()
    {
        return view('livewire.live-chat', [
            'messages' => Message::where('session_id', session()->getId())
                ->orderBy('created_at')
                ->take(100)
                ->get(),
        ]);
    }
}