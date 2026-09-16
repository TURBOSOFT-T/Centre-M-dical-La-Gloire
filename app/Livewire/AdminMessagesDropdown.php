<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AdminMessagesDropdown extends Component
{
    // Écouteur pour rafraîchir la bulle à la demande ou via votre script JS d'intervalle
    protected $listeners = ['refreshMessages' => '$refresh'];

    public function getUnreadCountProperty()
    {
        // Adaptez 'contact_forms' ou 'messages' selon le nom exact de votre table SQL
        // et le nom de votre colonne de statut (ex: 'is_read', 'status', etc.)
        return DB::table('contacts')
           
            ->count();
    }

    public function getRecentMessagesProperty()
    {
        return DB::table('contacts')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-messages-dropdown', [
            'unreadCount'    => $this->unreadCount,
            'recentMessages' => $this->recentMessages,
        ]);
    }
}