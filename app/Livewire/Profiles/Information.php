<?php

namespace App\Livewire\Profiles;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Http\Request;


class Information extends Component
{

    public $email, $nom,$prenom, $password, $password_confirmation,$old_password,$adresse,$phone;


    public function mount()
    {
        $this->email = Auth::user()->email;
        $this->nom = Auth::user()->nom;
        $this->prenom = Auth::user()->prenom;
        $this->adresse = Auth::user()->adresse;
        $this->phone = Auth::user()->phone;
        $this->old_password = Auth::user()->old_password;
    }

    

    public function update_user(Request $request)
    {
        $logout = false;
        $this->validate([
            'email' => 'email',
            'nom' => 'string',
            'prenom' => 'string',
            'adresse' => 'string',
            'phone' => 'numeric',
        ]);
        $user = User::find(Auth::id());
        if (!$user) {
            //flash error message
            session()->flash('error', 'Adresse e-mail ou mot de passe incorrect.');
            return;
        }

        $user->nom = $this->nom;
        $user->prenom = $this->prenom;
        $user->adresse = $this->adresse;
        $user->phone = $this->phone;

        if ($this->email != $user->email) {
            $user->email = $this->email;
            $logout = true;
        }
        if($this->email != $user->email){
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
        return view('livewire.profiles.information');
    }
}
