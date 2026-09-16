<?php

namespace App\Livewire\Transports;

use Livewire\Component;
use App\Models\Transport;

class ListVille extends Component
{
    public $ville, $frais;

    public function  save(){
        $this->validate([
            'ville' => ['required', 'string','max:255'],
            'frais' => ['required', 'numeric']
        ]);
        // Add or update ville in your database
         $transport = new Transport();
         $transport->ville = $this->ville;
            $transport->frais = $this->frais;
         $transport->save();
         $this->ville = '';
         $this->frais = '';
      //   $this->emit('villeSaved');
      //   return redirect()->route('transports');
          session()->flash('message', 'Client supprimé avec succès!');
    }


public function delete($id){
 $transport=   Transport::find($id)->delete();
   if($transport){
  //  $this->emit('villeDeleted');
    session()->flash('success', 'Transport deleted successfully');
   // return redirect()->route('transports');
   }
   // return redirect()->route('transports');
    session()->flash('message', 'Client supprimé avec succès!');
}


    public function render()
    {
        $transports = Transport::all();
        return view('livewire.transports.list-ville', compact('transports'));
    }
}
