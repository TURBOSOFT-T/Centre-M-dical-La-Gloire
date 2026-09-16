<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Shop;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UpdatePersonnels extends Component
{
    public $personnelId;
    public $nom;
    public $prenom;
    public $email;
    public $phone;
    public $shop_id;
    public $role_in_shop = 'caisse';

    /**
     * Initialisation des données de l'utilisateur à modifier
     */
    public function mount($personnelId)
    {
        $this->personnelId = $personnelId;
        $personnel = User::with('shops')->findOrFail($personnelId);

        $this->nom = $personnel->nom;
        $this->prenom = $personnel->prenom;
        $this->email = $personnel->email;
        $this->phone = $personnel->phone;
        $this->role_in_shop = $personnel->role ?? 'caisse';

        // Charge la première boutique rattachée si elle existe
        $firstShop = $personnel->shops->first();
        if ($firstShop) {
            $this->shop_id = $firstShop->id;
            $this->role_in_shop = $firstShop->pivot->role_in_shop ?? $this->role_in_shop;
        }
    }

    /**
     * Règles de validation dynamiques avec exclusion de l'utilisateur courant
     */
    protected function rules()
    {
        return [
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($this->personnelId)],
            'phone'        => ['required', 'numeric', Rule::unique('users', 'phone')->ignore($this->personnelId)],
            'shop_id'      => 'nullable|exists:shops,id',
            'role_in_shop' => 'required|in:caisse,personnel,vendeur,gerant,commercial',
        ];
    }

    public function render()
    {
        $shops = Shop::all();

        return view('livewire.update-personnels', compact('shops'));
    }

    public function update()
    {
        // 1. Validation des champs
        $this->validate();

        // 2. Recherche et mise à jour de l'utilisateur
        $personnel = User::findOrFail($this->personnelId);
        $personnel->nom   = $this->nom;
        $personnel->prenom = $this->prenom;
        $personnel->email  = $this->email;
        $personnel->phone  = $this->phone;
        $personnel->role   = $this->role_in_shop;
        $personnel->save();

        // 3. Synchronisation de la boutique et du rôle pivot
        if ($this->shop_id) {
            $personnel->shops()->sync([
                $this->shop_id => ['role_in_shop' => $this->role_in_shop]
            ]);
        } else {
            $personnel->shops()->detach();
        }

        // 4. Mise à jour du rôle Spatie
        $roleSpatie = Role::where('name', $this->role_in_shop)->first() 
                   ?? Role::where('name', 'caisse')->first();

        if ($roleSpatie) {
            $personnel->syncRoles([$roleSpatie]); 
        }

        // 5. Notification et redirection
        session()->flash('success', 'Personnel mis à jour avec succès !');

        return redirect()->route('personnels');
    }
}