<?php

namespace App\Livewire;
use App\Models\User;
use App\Models\historiques_connexion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Verifyregister extends Component
{


 public $code;
public function registerverify()
{
    $this->validate(['code' => 'required|numeric']);

    // --- CAS A : NOUVELLE INSCRIPTION ---
    if (session()->has('registration_data')) {
        $data = session('registration_data');
        $expectedCode = session('two_factor_code');
        $expiresAt = session('two_factor_expires_at');

        if ($this->code == $expectedCode && now()->isBefore($expiresAt)) {
            // Le code est bon, on crée l'utilisateur MAINTENANT
            $user = User::create($data);

            // Nettoyage session
            session()->forget(['registration_data', 'two_factor_code', 'two_factor_expires_at']);

            Auth::login($user);
            return redirect()->route('home');
        }
    }

    // --- CAS B : CONNEXION EXISTANTE (Votre code précédent) ---
    $userId = session('2fa_user_id');
    if ($userId) {
        $user = User::find($userId);
        if ($user && $user->two_factor_code === $this->code && now()->isBefore($user->two_factor_expires_at)) {
            $user->resetTwoFactor();
            Auth::login($user);
            session()->forget('2fa_user_id');
            return redirect()->route('dashboard');
        }
    }

    session()->flash('error', 'Code invalide ou expiré.');
}
    public function render()
    {
        return view('livewire.verifyregister');
    }
}
