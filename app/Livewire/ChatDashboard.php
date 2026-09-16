<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ChatDashboard extends Component
{
    public $sessionsOuvertes = []; // Liste des sessions ouvertes dans les onglets
    public $sessionActive = null;    // La session actuellement affichée au premier plan
    public $nouveauMessage = '';

    protected $listeners = ['refreshAdminChat' => '$refresh'];

    public function selectionnerSession($sessionId)
    {
        // Si la session n'est pas encore dans les onglets ouverts, on l'ajoute
        if (!in_array($sessionId, $this->sessionsOuvertes)) {
            $this->sessionsOuvertes[] = $sessionId;
        }
        
        $this->sessionActive = $sessionId;
        $this->dispatch('scroll-admin-chat');
    }

    public function fermerOnglet($sessionId)
    {
        // Retire la session des onglets ouverts
        $this->sessionsOuvertes = array_values(array_diff($this->sessionsOuvertes, [$sessionId]));
        
        // Si on a fermé la session active, on bascule sur une autre ou on réinitialise
        if ($this->sessionActive === $sessionId) {
            $this->sessionActive = count($this->sessionsOuvertes) > 0 ? $this->sessionsOuvertes[0] : null;
        }
    }

    public function envoyer()
    {
        if (!$this->sessionActive) return;

        $this->validate([
            'nouveauMessage' => 'required|string|max:1000',
        ]);

        Message::create([
            'session_id' => $this->sessionActive,
            'contenu' => $this->nouveauMessage,
            'expediteur' => 'admin',
        ]);

        $this->nouveauMessage = '';
        $this->dispatch('scroll-admin-chat');
    }

    public function render()
    {
        // Récupère toutes les discussions actives triées par récence
        $discussions = Message::select('session_id', DB::raw('MAX(created_at) as dernier_message'))
            ->groupBy('session_id')
            ->orderBy('dernier_message', 'desc')
            ->get()
            ->map(function ($chat) {
                $dernierMsg = Message::where('session_id', $chat->session_id)->orderBy('created_at', 'desc')->first();
                return [
                    'session_id' => $chat->session_id,
                    'aperçu' => $dernierMsg ? $dernierMsg->contenu : '',
                    'temps' => $dernierMsg ? $dernierMsg->created_at->diffForHumans() : '',
                ];
            });

        // Récupère les messages uniquement de la session active sur l'écran principal
        $messagesSelectionnes = [];
        if ($this->sessionActive) {
            $messagesSelectionnes = Message::where('session_id', $this->sessionActive)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('livewire.chat-dashboard', [
            'discussions' => $discussions,
            'messages' => $messagesSelectionnes
        ])->layout('layouts.app');
    }
}