<?php

namespace App\Livewire;

use App\Models\historiques_connexion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;


class Connexion extends Component
{

    public $email, $password, $phone;


    public $passwordVisible = true; // Par défaut, le mot de passe est visible
    public function togglePasswordVisibility()
    {
        $this->passwordVisible = !$this->passwordVisible;
    }




    public function render()
    {
        return view('livewire.connexion');
    }

    public function connexion()
    {
        $this->validate([
            'phone' => 'required|exists:users,phone',
            'password' => 'string|required',
        ], [
            'phone.required' => 'Veuillez entrer votre numéro de téléphone',
            'phone.exists' => 'Ce numéro de téléphone n\'existe pas',
            'password.string' => 'Veuillez entrer votre mot de passe',
            'password.required' => 'Veuillez entrer votre mot de passe',
        ]);

        // Recherche de l'utilisateur par son numéro de téléphone
        $user = User::where('phone', $this->phone)->first();

        if ($user && Hash::check($this->password, $user->password)) {

            // Attention : Vérifie si le rôle "client" ou un autre remplace "user" suite à ta migration
            if ($user->role == "user") {
                session()->flash('error', 'Impossible de se connecter !');
                return;
            } else {
                Auth::login($user);

                // Enregistrement de l'historique de connexion si l'IP n'existe pas encore
                $count = historiques_connexion::where('ip_address', request()->ip())->count();
                if ($count == 0) {
                    $userLogin = new historiques_connexion();
                    $userLogin->user_id = $user->id;
                    $userLogin->ip_address = request()->ip();
                    $userLogin->user_agent = request()->header('User-Agent');
                    $userLogin->save();
                }

                return redirect()->route('dashboard');
            }
        } else {
            session()->flash('error', 'Numéro de téléphone ou mot de passe incorrect.');
        }
    }
}
