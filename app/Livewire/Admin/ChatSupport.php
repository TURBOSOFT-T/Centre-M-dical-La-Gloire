<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Message;

class ChatSupport extends Component
{
    public $conversationSelectionneeId = null; // Contiendra le session_id sélectionné
    public $nouveauMessage = '';

    protected $listeners = ['refreshChat' => '$refresh'];

    public function selectionnerConversation($sessionId)
    {
        $this->conversationSelectionneeId = $sessionId;
        $this->dispatch('scroll-chat-bottom');
    }

    public function envoyer()
    {
        $this->validate([
            'nouveauMessage' => 'required|string|max:5000',
        ]);

        if (!$this->conversationSelectionneeId) {
            return;
        }

        // On enregistre le message de l'admin
        Message::create([
            'session_id' => $this->conversationSelectionneeId,
            'contenu'    => $this->nouveauMessage,
            'expediteur' => 'admin',
        ]);

        $this->nouveauMessage = '';

        // 1. Fait défiler le chat de l'admin vers le bas
        $this->dispatch('scroll-chat-bottom');

        // 2. Déclenche le BIP SONORE et le TOAST SweetAlert2 chez le CLIENT
        $this->dispatch('bip-client');
    }

    public function supprimerMessagesDuClient()
    {
        if (!$this->conversationSelectionneeId) {
            return;
        }

        // Supprime uniquement les messages où l'expéditeur n'est pas l'admin
        Message::where('session_id', $this->conversationSelectionneeId)
            ->where('expediteur', '!=', 'admin')
            ->delete();

        // Alerte JavaScript (SweetAlert) pour fermer le chargement et afficher le succès
        $this->dispatch('client-messages-effaces');
        $this->dispatch('scroll-chat-bottom');
    }

    public function toutEffacer()
    {
        if (!$this->conversationSelectionneeId) {
            return;
        }

        // CORRECTION : Supprime uniquement les messages de CETTE session, pas toute la table !
        Message::where('session_id', $this->conversationSelectionneeId)->delete();

        // Réinitialise l'état du chat actuel
        $this->conversationSelectionneeId = null;

        // Alerte JavaScript (SweetAlert) pour fermer le chargement et afficher le succès
        $this->dispatch('tout-a-ete-efface');
    }

    public function render()
    {
        // On regroupe les conversations par session_id unique
        $conversations = Message::select('session_id')
            ->selectRaw('MAX(created_at) as dernier_message_at')
            ->groupBy('session_id')
            ->orderBy('dernier_message_at', 'desc')
            ->get();

        $messages = collect();
        if ($this->conversationSelectionneeId) {
            $messages = Message::where('session_id', $this->conversationSelectionneeId)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('livewire.admin.chat-support', [
            'conversations' => $conversations,
            'messages' => $messages
        ])->layout('layouts.admin');
    }
}