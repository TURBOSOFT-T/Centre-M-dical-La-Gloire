<?php

namespace App\Livewire;

use App\Models\Shop;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AddShops extends Component
{
    public $name,$prenom,$email,$phone;

    public function render()
    {
        return view('livewire.add-shops');
    }

    public function create(){
        $this->validate([
            'name' =>'required|string',
          
            'email' =>'required|email|unique:shops,email',
            'phone' =>'required|numeric',
        ]);

        
        $shops = new Shop();
        $shops->name = $this->name;
      
        $shops->email = $this->email;
        $shops->phone = $this->phone;
        $shops->role = "seller";
        $shops->password = Hash::make('123456789');
        $shops->save();

        $role = Role::where('name', 'seller')->first();
        if ( $role) {
            $shops->assignRole($role->id); 
        }

        return redirect()->route('shops');


    }
}
