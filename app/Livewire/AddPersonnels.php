<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Shop;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AddPersonnels extends Component
{
    // Propriétés du formulaire
    public $nom;
    public $prenom;
    public $email;
    public $phone;
    public $shop_id;
    public $role_in_shop = 'caisse'; // Définit "caisse" par défaut

    /**
     * Règles de validation
     */
    protected function rules()
    {
        return [
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|numeric|unique:users,phone',
            'shop_id'      => 'nullable|exists:shops,id',
          //  'role_in_shop' => 'required|in:caisse,personnel,vendeur,gerant,commercial',
        ];
    }

    public function render()
    {
        $shops = Shop::all();

        return view('livewire.add-personnels', compact('shops'));
    }

    public function create()
    {
        // 1. Validation des champs
        $this->validate();

        $defaultPassword = '123456789';

        // 2. Création et enregistrement de l'utilisateur en base de données
        $personnel = new User();
        $personnel->nom      = $this->nom;
        $personnel->prenom   = $this->prenom;
        $personnel->email    = $this->email;
        $personnel->phone    = $this->phone;
        
        // Rôle principal dans la table users
        $personnel->role     = $this->role_in_shop; 
        
        $personnel->password = Hash::make($defaultPassword);
        $personnel->save();

        // 3. Liaison avec la table pivot shop_user
        if ($this->shop_id) {
            $personnel->shops()->attach($this->shop_id, [
                'role_in_shop' => $this->role_in_shop,
            ]);
        }

        // 4. Attribution du rôle Spatie Permission
        $roleSpatie = Role::where('name', $this->role_in_shop)->where('guard_name', 'web')->first() 
                   ?? Role::where('name', 'caisse')->where('guard_name', 'web')->first();

        if ($roleSpatie) {
            $personnel->assignRole($roleSpatie); 
        }

        // 5. Envoi du SMS avec les identifiants de connexion via Etech Keys
        $telephonePersonnel = $this->phone ?? null;

       
            // Message SMS corrigé avec l'email et le mot de passe temporaire
            $messageTexte = "Bonjour " . $this->nom . ", votre compte " . strtoupper($this->role_in_shop) . " a ete cree chez SWOOT BIO. Email: " . $this->email . " / PWD: " . $defaultPassword;

            try {
                $response = Http::get('https://sms.etech-keys.com/ss/api.php', [
                    'login'         => '655262413',
                    'password'      => '655262413',
                    'sender_id'     => 'ETECHKEYS',
                  'destinataire' => '237' . $this->phone, // Adaptez '237' selon votre pays
                    'message'       => $messageTexte,
                    'ext_id'        => $this->phone,
                    'programmation' => 0
                ]);

                if (!$response->successful()) {
                    Log::error("Erreur HTTP lors de l'envoi du SMS de création de personnel (ID: " . $personnel->id . ") : " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Exception SMS création personnel (ID: " . $personnel->id . ") : " . $e->getMessage());
            }
        

        // 6. Notification et redirection
        session()->flash('success', 'Utilisateur créé, rattaché à la boutique et SMS envoyé avec succès !');

        return redirect()->route('personnels');
    }
}