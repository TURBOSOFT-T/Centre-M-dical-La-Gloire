<?php

namespace App\Livewire\Banners;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\Laravel\Facades\Image;

class Update extends Component
{
    use WithFileUploads;
    
    public $banner, $titre, $sous_titre, $photo, $image;

    public function mount($banner)
    {
        $this->banner = $banner;
        $this->titre = $banner->titre;
        $this->sous_titre = $banner->sous_titre;
        $this->image = $banner->image;
    }

    public function render()
    {
        return view('livewire.banners.update');
    }

    private function compressAndStoreBanner($imageFile)
    {
        $filename = 'banners/' . uniqid() . '.webp';
        
        $image = Image::read($imageFile->getRealPath());
        $image->scale(width: 1920); // Largeur optimisée pour les bannières
        
        $encoded = $image->toWebp(80); // Compression à 80%

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    public function update()
    {
        $this->validate([
            'titre' => 'nullable|string|max:500',
            'sous_titre' => 'nullable|string|max:2050',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'titre.max' => 'Le titre ne doit pas dépasser 500 caractères',
            'sous_titre.max' => 'Le sous titre ne doit pas dépasser 2050 caractères',
            'photo.image' => 'Le fichier doit être une image',
            'photo.mimes' => 'Le fichier doit être au format jpg, jpeg, png, webp',
            'photo.max' => 'La photo ne doit pas dépasser 10 Mo',
        ]);

        $OldBanner = $this->banner;

        if ($this->photo) {
            // Supprimer l'ancienne image si elle existe
            if ($OldBanner->image && Storage::disk('public')->exists($OldBanner->image)) {
                Storage::disk('public')->delete($OldBanner->image);
            }
            
            // Compresser et enregistrer la nouvelle image au format WebP
            $OldBanner->image = $this->compressAndStoreBanner($this->photo);
        }

        $OldBanner->titre = $this->titre;
        $OldBanner->sous_titre = $this->sous_titre;
        $OldBanner->save();

        $this->image = $OldBanner->image;

        // Flash success message
        session()->flash('success', 'Les modifications ont été enregistrées.....');
    }
}