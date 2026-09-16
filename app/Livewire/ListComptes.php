<?php

namespace App\Livewire;

use App\Models\clients;
use App\Models\Historique_points;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListComptes extends Component
{
    use WithPagination;

   
    public $key;
    public $showModal = false;
    public $solde = 1;
    public $selectedClient;


    public $transactions = [];
    public $transactionPoints = [];



    public $clientId;


    public $isModalOpen = false;
    public $isModalPointOpen = false;

    public function openTransactionHistoryModal($clientId)
    {
        $this->transactions = Transaction::where('user_id', $clientId)->get();
        $this->isModalOpen = true;
    }


    public function openPointsHistoryModal($clientId){
        $this->transactionPoints = Historique_points::where('user_id', $clientId)->get();
        $this->isModalPointOpen = true;
    }


    public function closeModal()
    {
        $this->isModalOpen = false;
    }



    public function closeModalPoint()
    {
        $this->isModalPointOpen = false;
    }



    public function openModal($clientId)
    {
        $this->selectedClient = $clientId;
        $this->solde = 1;
        $this->showModal = true;
    }

    public function addSolde()
    {
        $client = User::find($this->selectedClient);
        if ($client) {
            $client->solde += $this->solde;
            $client->save();
            Transaction::create([
                'user_id' => $client->id,
                'montant' => $this->solde,
                'type' => 'credit',
                'description' => 'Ajout manuel au solde',
            ]);

            session()->flash('message', 'Solde ajouté avec succès.');
            $this->showModal = false;
        }
    }

/*     public function render()
    {
        $clients =User::where('role', 'client','personel')
      ->  Orderby("created_at");
        if (isset($this->key)) {
            $clients->where('nom', 'like', '%' . $this->key . '%')
                ->orWhere('phone', 'like', '%' . $this->key . '%')
                ->orWhere('prenom', 'like', '%' . $this->key . '%');
        }
        $clients = $clients->paginate(30);
        $total = clients::count();
        return view('livewire.list-clients', compact('clients','total'));
    }
 */

    public function render()
{
    $clients = User::query()
        ->orderBy('created_at', 'desc');

    if (isset($this->key)) {

        $clients->where(function ($query) {

            $query->where('nom', 'like', '%' . $this->key . '%')
                ->orWhere('prenom', 'like', '%' . $this->key . '%')
                ->orWhere('phone', 'like', '%' . $this->key . '%');

        });

    }

    $clients = $clients->paginate(30);


    $total = User::count();


    return view('livewire.list-comptes', compact('clients', 'total'));
}


    public function filtrer()
    {
        //reset page
        $this->resetPage();
    }

    public function delete($id){
        //delete client
        $client = User::find($id);
        if($client){
            $client->delete();
            //flash message
            session()->flash('message', 'Client supprimé avec succès!');
        }
    }
}
