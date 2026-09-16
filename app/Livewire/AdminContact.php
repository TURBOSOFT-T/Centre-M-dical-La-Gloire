<?php

namespace App\Livewire;

use App\Models\config;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\Laravel\Facades\Image;

class AdminContact extends Component
{
    use WithFileUploads;

    public $logo,$icon,$logo2,$icon2,$frais, $logoHeader, $telephone,$addresse, $email,$description, $logofooter, $logofooter2,
   $satisfaction, $icone_satisfaction, $icone_satisfaction2, $des_satisfaction,
   $annee, $icone_annee, $icone_annee2, $des_annee,
   $prix, $icone_prix, $des_prix,$icone_prix2,
   $titre_apropos, $des_apropos, $image_apropos,$image_apropos0,
   $titre_apropos1, $des_apropos1, $image_apropos1, $image_apropos12,
   $titre_apropos2, $des_apropos2, $image_apropos2, $image_apropos22,
   $image_contact, $image_shop, $image_about,
   $image_contact2, $image_shop2, $image_about2,
   $image_login, $image_register,
   $image_login2, $image_register2,
   $titre_annee, $titre_prix, $titre_satisfaction; 
    public $slogan;

    public function mount(){
        $config = config::first();
        if ($config) {
            $this->icone_annee2 = $config->icone_annee;
            $this->image_shop2 = $config->image_shop;
            $this->icone_satisfaction2 = $config->icone_satisfaction;
            $this->icone_prix2 = $config->icone_prix;
            $this->image_apropos0 = $config->image_apropos;
            $this->image_apropos12 = $config->image_apropos1;
            $this->image_apropos22 = $config->image_apropos2;
            $this->titre_apropos2 = $config->titre_apropos2;
            $this->image_contact2 = $config->image_contact;
            $this->image_shop2 = $config->image_shop;
            $this->image_about2 = $config->image_about;
            $this->image_login2 = $config->image_login;
            $this->image_register2 = $config->image_register;
           
            $this->logo2 = $config->logo;
       
            $this->logofooter2 = $config->logofooter;
            $this->icon2 = $config->icon;
            $this->frais = $config->frais;
            $this->logoHeader= $config->logoHeader;
            $this->email=$config->email;
            $this->telephone=$config->telephone;
            $this->addresse=$config->addresse;
            $this->description=$config->description;
            $this->slogan=$config->slogan;

            $this->annee=$config->annee;
            $this->titre_annee=$config->titre_annee;
            $this->des_annee = $config->des_annee;
            $this->satisfaction=$config->satisfaction;
            $this->titre_satisfaction=$config->titre_satisfaction;
            $this->des_satisfaction = $config->des_satisfaction;
            $this->prix = $config->prix;
            $this->des_prix = $config->des_prix;
            $this->titre_prix = $config->titre_prix;

            $this->titre_apropos = $config->titre_apropos;
            $this->des_apropos = $config->des_apropos;
            $this->titre_apropos1 = $config->titre_apropos1;  
            $this->des_apropos1 = $config->des_apropos1; 
            $this->titre_apropos2 = $config->titre_apropos2;
            $this->des_apropos2 = $config->des_apropos2;
        }
    }

    public function render()
    {
        return view('livewire.admin-contact');
    }

    // Méthode mutualisée pour compresser, convertir en webp et stocker
   private function compressAndStoreImage($imageFile, $folder, $oldImagePath = null)
    {
        // 1. SI UN ANCIEN FICHIER EXISTE, ON LE SUPPRIME DU SERVEUR
        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        // 2. ON GÉNÈRE LE NOM ET ON COMPRESSE LE NOUVEAU EN WEBP
        $filename = $folder . '/' . uniqid() . '.webp';
        
        $image = Image::read($imageFile->getRealPath());
        $image->scale(width: 1200); 
        
        $encoded = $image->toWebp(80);

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    public function update_form(){
        $this->validate([
            'logo' => 'image|nullable|max:10240',
            'logoHeader' => 'image|nullable|max:10240',
            'icon' => 'image|nullable|max:10240',
            'logofooter' => 'image|nullable|max:10240',
            'image_apropos' => 'image|nullable|max:10240',
            'image_apropos1' => 'image|nullable|max:10240',
            'image_apropos2' => 'image|nullable|max:10240',
            'image_contact' => 'image|nullable|max:10240',
            'image_shop' => 'image|nullable|max:10240',
            'image_about' => 'image|nullable|max:10240',
            'image_login' => 'image|nullable|max:10240',
            'image_register' => 'image|nullable|max:10240',
            'icone_annee' => 'image|nullable|max:10240',
            'icone_satisfaction' => 'image|nullable|max:10240',
            'icone_prix' => 'image|nullable|max:10240',
            'frais' => 'nullable|numeric',
            'satisfaction' => 'nullable|numeric',
            'telephone' => 'nullable|numeric',
            'email' => 'nullable',
            'addresse' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
        ]);

        $config = config::first();

        // Traitement sécurisé avec compression et nettoyage de chaque image
        if ($this->logo) {
            $config->logo = $this->compressAndStoreImage($this->logo, 'logo', $this->logo2);
        }
        if ($this->logoHeader) {
            $config->logoHeader = $this->compressAndStoreImage($this->logoHeader, 'logoHeader', $this->logoHeader2 ?? null);
        }
        if ($this->icon) {
            $config->icon = $this->compressAndStoreImage($this->icon, 'icon', $this->icon2);
        }
        if ($this->logofooter) {
            $config->logofooter = $this->compressAndStoreImage($this->logofooter, 'logofooter', $this->logofooter2);
        }
        if ($this->image_apropos) {
            $config->image_apropos = $this->compressAndStoreImage($this->image_apropos, 'image_apropos', $this->image_apropos0);
        }
        if ($this->image_apropos1) {
            $config->image_apropos1 = $this->compressAndStoreImage($this->image_apropos1, 'image_apropos1', $this->image_apropos12);
        }
        if ($this->image_apropos2) {
            $config->image_apropos2 = $this->compressAndStoreImage($this->image_apropos2, 'image_apropos2', $this->image_apropos22);
        }
        if ($this->image_contact) {
            $config->image_contact = $this->compressAndStoreImage($this->image_contact, 'image_contact', $this->image_contact2);
        }
        if ($this->image_shop) {
            $config->image_shop = $this->compressAndStoreImage($this->image_shop, 'image_shop', $this->image_shop2);
        }
        if ($this->image_about) {
            $config->image_about = $this->compressAndStoreImage($this->image_about, 'image_about', $this->image_about2);
        }
        if ($this->image_login) {
            $config->image_login = $this->compressAndStoreImage($this->image_login, 'image_login', $this->image_login2);
        }
        if ($this->image_register) {
            $config->image_register = $this->compressAndStoreImage($this->image_register, 'image_register', $this->image_register2);
        }
        if ($this->icone_annee) {
            $config->icone_annee = $this->compressAndStoreImage($this->icone_annee, 'icon', $this->icone_annee2);
        }
        if ($this->icone_satisfaction) {
            $config->icone_satisfaction = $this->compressAndStoreImage($this->icone_satisfaction, 'icon', $this->icone_satisfaction2);
        }
        if ($this->icone_prix) {
            $config->icone_prix = $this->compressAndStoreImage($this->icone_prix, 'icon', $this->icone_prix2);
        }

        // Mise à jour des champs texte
        $config->frais = $this->frais;
        $config->telephone = $this->telephone;
        $config->email = $this->email;
        $config->addresse = $this->addresse;
        $config->description = $this->description;
        $config->des_annee = $this->des_annee;
        $config->titre_annee = $this->titre_annee;
        $config->des_satisfaction = $this->des_satisfaction;
        $config->titre_satisfaction = $this->titre_satisfaction;
        $config->slogan = $this->slogan;
        $config->des_prix = $this->des_prix;
        $config->titre_prix = $this->titre_prix;
        $config->titre_apropos = $this->titre_apropos;
        $config->des_apropos = $this->des_apropos;
        $config->titre_apropos1 = $this->titre_apropos1;
        $config->des_apropos1 = $this->des_apropos1;
        $config->titre_apropos2 = $this->titre_apropos2;
        $config->des_apropos2 = $this->des_apropos2;

        if($config->save()){
            session()->flash('info', 'Vos modifications ont été enregistrées.');
        }else{
            session()->flash('danger', 'Vos modifications n\'ont pas été enregistrées.');
        }
    }
}