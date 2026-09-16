<?php

namespace App\Livewire\Banners;

use App\Models\banner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class Add extends Component
{
    use WithFileUploads;
    
    public $photo, $titre, $sous_titre, $banner;

    public function render()
    {
        return view('livewire.banners.add');
    }

    // Méthode de compression spécifique pour les bannières (plus larges, ex: 1920px max)
    private function compressAndStoreBanner($imageFile)
    {
        $filename = 'bannners/' . uniqid() . '.webp';
        
        $image = Image::read($imageFile->getRealPath());
        $image->scale(width: 1920); // Les bannières nécessitent plus de largeur
        
        $encoded = $image->toWebp(80); // 80% de qualité pour un excellent compromis

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    public function save()
    {
        $this->validate([
            'titre' => 'nullable|string|max:500',
            'sous_titre' => 'nullable|string|max:2050',
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'photo.required' => 'La photo est obligatoire',
            'photo.image' => 'La photo doit être une image',
            'photo.mimes' => 'La photo doit être au format jpg,jpeg,png,webp',
            'photo.max' => 'La photo ne doit pas dépasser 10 Mo',
            'titre.max' => 'Le titre ne doit pas dépasser 500 caractères',
            'sous_titre.max' => 'Le sous titre ne doit pas dépasser 2050 caractères',
        ]);

        $banner = new banner();
        $banner->titre = $this->titre;
        $banner->sous_titre = $this->sous_titre;
        
        // Compression et enregistrement au format WebP
        $banner->image = $this->compressAndStoreBanner($this->photo);
        
        $banner->save();

        session()->flash('success', 'Banner ajouté avec succès');
        $this->reset();
        $this->dispatch('BannerAdded');
    }
}