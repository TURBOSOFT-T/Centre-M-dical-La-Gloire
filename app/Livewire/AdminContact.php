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

    public $logo,$icon,$logo2,$icon2, $telephone,$addresse, $email,$description;
  

    public function mount(){
        $config = config::first();
        if ($config) {
          
       
           
            $this->logo2 = $config->logo;
       
           
            $this->icon2 = $config->icon;
        
            $this->email=$config->email;
            $this->telephone=$config->telephone;
            $this->addresse=$config->addresse;
            $this->description=$config->description;
            

        

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
            
            'icon' => 'image|nullable|max:10240',
         
       
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
     
        if ($this->icon) {
            $config->icon = $this->compressAndStoreImage($this->icon, 'icon', $this->icon2);
        }
    
  
  
        // Mise à jour des champs texte
      
        $config->telephone = $this->telephone;
        $config->email = $this->email;
        $config->addresse = $this->addresse;
        $config->description = $this->description;
 

        if($config->save()){
            session()->flash('info', 'Vos modifications ont été enregistrées.');
        }else{
            session()->flash('danger', 'Vos modifications n\'ont pas été enregistrées.');
        }
    }
}