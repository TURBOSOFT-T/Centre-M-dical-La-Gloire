<?php

namespace App\Livewire\Front;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ForgetPassword extends Component
{
    public $phone, $enter_code, $password, $password_confirmation;
    public $step = 1;

    public function render()
    {
        return view('livewire.front.forget-password');
    }

    public function form_1()
    {
        $this->validate([
            'phone' => 'required|exists:users,phone',
        ], [
            'phone.exists' => 'Numéro de téléphone incorrect ou introuvable',
            'phone.required' => 'Veuillez entrer votre numéro de téléphone',
        ]);

        $user = User::where('phone', $this->phone)->first();

        // Générer un code aléatoire de 6 chiffres
        $code = rand(100000, 999999);

        // Stocker le code et le téléphone en session pour les étapes suivantes
        session(['reset_code' => $code, 'reset_phone' => $this->phone]);

        // Message SMS avec le code de réinitialisation
        $messageTexte = "Bonjour " . ($user->nom ?? '') . ", votre code de reinitialisation SWOOT BIO est : " . $code;

        // Envoi du SMS via Etech Keys
        try {
            $response = Http::get('https://sms.etech-keys.com/ss/api.php', [
                'login'         => '655262413',
                'password'      => '655262413',
                'sender_id'     => 'ETECH KEYS', // Ou 'ETECHKEYS' si le message ne part pas chez certains opérateurs
                'destinataire'  => $this->phone,
                'message'       => $messageTexte,
                'ext_id'        => $user->id,
                'programmation' => 0
            ]);

            if (!$response->successful()) {
                Log::error("Erreur HTTP envoi SMS réinitialisation (ID: " . $user->id . ") : " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception SMS réinitialisation (ID: " . $user->id . ") : " . $e->getMessage());
        }

        session()->flash('success', 'Un code de réinitialisation vous a été envoyé par SMS.');
        $this->step = 2;
    }

    public function form_2()
    {
        $this->validate([
            'enter_code' => 'required|integer',
        ], [
            'enter_code.required' => 'Code incorrect',
            'enter_code.integer' => 'Code incorrect',
        ]);

        $sessionCode = session('reset_code');

        if (!$sessionCode || $sessionCode != $this->enter_code) {
            session()->flash('error', 'Code entré est incorrect !');
            return;
        }

        $this->step = 3;
    }

    public function form_3()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Mot de passe requis',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
        ]);

        // Récupérer l'utilisateur via le téléphone stocké en session
        $phone = session('reset_phone');
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            session()->flash('error', 'Une erreur est survenue. Veuillez recommencer.');
            $this->step = 1;
            return;
        }

        $user->password = bcrypt($this->password);
        $user->save();

        // Nettoyer la session
        session()->forget(['reset_code', 'reset_phone']);

        Auth::login($user);
        return redirect('/');
    }
}