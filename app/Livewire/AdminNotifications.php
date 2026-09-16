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

    public function delete($id){
        if ($id) {
            $notification=notifications::findOrFail($id);
            $notification->delete();
        }
    }

public function deleteAll()
{
    // Supprime définitivement toutes les notifications de votre table personnalisée
    \App\Models\notifications::query()->delete();
}
}
