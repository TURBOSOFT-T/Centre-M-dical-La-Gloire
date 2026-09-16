<?php

namespace App\Livewire\Profiles;

use Livewire\Component;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends Component
{

    
    public $email, $nom, $password, $password_confirmation,$old_password,$adresse,$phone;


    public function mount()
    {
        $this->email = Auth::user()->email;
        $this->nom = Auth::user()->nom;
        $this->adresse = Auth::user()->adresse;
        $this->phone = Auth::user()->phone;
    }

    

    public function update_user()
    {
        $logout = false;
        $this->validate([
            'email' => 'required|email',
            'nom' => 'required|string',
            'adresse' => 'required|string',
            'phone' => 'required|string',
        ]);
        $user = User::find(Auth::id());
        if (!$user) {
            //flash error message
            session()->flash('error', 'Adresse e-mail ou mot de passe incorrect.');
            return;
        }

        $user->nom = $this->nom;
        $user->adresse = $this->adresse;
        $user->phone = $this->phone;

        if ($this->email != $user->email) {
            $user->email = $this->email;
            $logout = true;
        }

        if (!is_null($this->password)) {
            $this->validate([
                'password' => 'required|confirmed|min:9',
                'old_password' => 'required|string|min:9'
            ]);
            if (!Hash::check($this->old_password, $user->password)) {
                session()->flash('error', 'Votre mot de passe actuel est incorrect.');
                return;
            }
            $user->password = Hash::make($this->password);
            $logout = true;
        }
        $user->save();
        //flash message
        session()->flash('success', 'Vos modifications ont été enregistrées avec succès.');
        if ($logout) {
            Auth::logout();
            return redirect()->route('login');
        }

    }
    public function render()
    {
        return view('livewire.profiles.update-password');
    }
}
