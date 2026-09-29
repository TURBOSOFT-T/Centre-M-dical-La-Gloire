<?php

namespace App\Livewire;

use App\Models\notifications;
use Livewire\Component;

class AdminNotifications extends Component
{
    protected $listeners = ['notificationUpdated' => '$refresh'];

    public function render()
    {

        //mark all notification as read
        notifications::where("statut","unread")->update(['statut' => 'read']);

        $notifications = notifications::Orderby("id","Desc")->take(10)->get();
        return view('livewire.admin-notifications', compact("notifications"));
    }

   public function delete($id)
    {
        $notification = \App\Models\notifications::find($id);
        
        if ($notification) {
            $notification->delete();
        }
        
        // Optionnel : Vous pouvez ajouter un message flash ou un événement si nécessaire
    }

    /**
     * Supprime toutes les notifications de la table
     */
    public function deleteAll()
    {
        // Supprime toutes les lignes de la table notifications en toute sécurité
        notifications::query()->delete();
    }
}
