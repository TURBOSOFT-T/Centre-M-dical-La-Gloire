<?php

namespace App\Livewire\Front;

use App\Mail\register as MailRegister;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $nom;
    public $prenom;
    public $phone;       // Remplacement de l'identifiant principal
    public $email;       // Devient optionnel
    public $password;
    public $password_confirmation;
    public $isRegistered = false;

    protected $rules = [
        'nom' => 'required|string|max:255', // Rendu obligatoire pour SWOOT BIO, plus propre
        'prenom' => 'nullable|string|max:255',
        'phone' => 'required|unique:users,phone',
        'email' => 'nullable|email|unique:users,email', // Nullable suite à la modif de la BDD
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required|min:8',
    ];

    protected $messages = [
        'nom.required' => 'Le nom est obligatoire',
        'phone.required' => 'Le numéro de téléphone est obligatoire',
        'phone.unique' => 'Ce numéro de téléphone est déjà utilisé',
        'email.email' => 'L\'email n\'est pas valide',
        'email.unique' => 'L\'email existe déjà',
        'password.required' => 'Le mot de passe est obligatoire',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
        'password.confirmed' => 'Les mots de passe ne correspondent pas',
        'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire',
        'password_confirmation.min' => 'La confirmation du mot de passe doit contenir au moins 8 caractères',
    ];


    public function save()
    {
        $this->validate();

        $user = User::create([
            'nom' => $this->nom,
    
            'phone' => $this->phone,
        
            'password' => Hash::make($this->password),
            'role' => 'client', 
        ]);

        $this->isRegistered = true;
 
       
        Auth::login($user);
        session()->flash('success', 'Votre compte a été créé avec succès!');

        return redirect()->route('home');
    }


    
public function save2()
    {
        $this->validate();

        // 1. Générer le code de validation à 6 chiffres
        $code = rand(100000, 999999);

        // 2. Stocker les données d'inscription en session (temporairement)
        session([
            'registration_data' => [
                'nom' => $this->nom,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'role' => 'client',
            ],
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(15),
        ]);

        // 3. Envoyer le code de validation par SMS au numéro saisi dans le formulaire
        $telephoneClient = $this->phone ?? null;

        if ($telephoneClient) {
            $nomClient = $this->nom ?? 'Client';
            $messageTexte = "Bonjour " . $nomClient . ", votre code de validation SWOOT BIO est : " . $code . ". Validez votre inscription.";

            try {
                $response = \Illuminate\Support\Facades\Http::get('https://sms.etech-keys.com/ss/api.php', [
                    'login'         => '655262413',
                    'password'      => '655262413',
                    'sender_id'     => 'SWOOT BIO',
                    'destinataire'  => $telephoneClient,
                    'message'       => $messageTexte,
                    'ext_id'        => time(), // Identifiant unique basé sur le timestamp
                    'programmation' => 0
                ]);

              

                if (!$response->successful()) {
                    \Log::error("Erreur HTTP lors de l'envoi du SMS d'inscription (Tel: " . $telephoneClient . ") : " . $response->body());
                }
            } catch (\Exception $e) {
                \Log::error("Exception SMS inscription (Tel: " . $telephoneClient . ") : " . $e->getMessage());
            }
        }

        session()->flash('success', 'Un code de validation a été envoyé à votre numéro de téléphone.');

        return redirect()->route('register2fa.index');
    }

    public function render()
    {
        return view('livewire.front.register');
    }
}