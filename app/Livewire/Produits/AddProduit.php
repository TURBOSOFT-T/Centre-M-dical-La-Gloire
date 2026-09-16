<?php

namespace App\Livewire\Produits;

use App\Models\{produits, Category, Marque};
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Http\Traits\{TailleProduit, ListColor, ListColors};
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class AddProduit extends Component
{
    use WithFileUploads;
    use ListColor;
    use TailleProduit;

    public $nom, $tags,  $prix, $category_id, $photo, $photos, $prix_achat, $photo2, $photos2, $produit, $reference, $description, $marque_id;
    public $free_shipping = false;
    public $is_new = false;
    public $meta_description;
    public $avec_commission = false;
    public $commission;

    public function mount($produit = null)
    {
        if ($produit) {
            $this->produit = $produit;
            $this->nom = $produit->nom;
            $this->tags = $produit->tags;
            $this->category_id = $produit->category_id;
            $this->marque_id = $produit->marque_id;

            $this->reference = $produit->reference;
            $this->prix = $produit->prix;
            $this->prix_achat = $produit->prix_achat;
            $this->photo2 = $produit->photo;
            $this->photos2 = $produit->photos;
            $this->description = $produit->description;
            $this->free_shipping = (bool)$produit->free_shipping;
            $this->is_new = (bool)$produit->is_new;
            $this->meta_description = $produit->meta_description;
            $this->avec_commission = (bool)$produit->avec_commission;
            $this->commission = $produit->commission;
        }
    }

    public function render()
    {
        return view('livewire.produits.add-produit', [
            'categories' => Category::all(),
            'marques'    => Marque::all(),
            'couleurs'   => $this->getListColor(),
            'tailles'    => $this->getListTailleProduit(),
        ]);
    }
    private function compressAndStoreImage($imageFile)
    {
        $filename = 'produits/' . uniqid() . '.webp';

        // Initialisation du manager avec le driver GD
        $manager = new ImageManager(new Driver());

        // Lecture et redimensionnement
        $img = $manager->read($imageFile->getRealPath())
            ->scale(width: 1000); // Redimensionne proportionnellement à 1000px de large max

        // Encodage en WebP avec 80% de qualité et sauvegarde sur le disque public
        $encoded = $img->toWebp(80);

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
    public function create()
    {
        $this->validate([
            'nom'               => 'required|string',
            'description'       => 'required|string|max:50000',
            'meta_description'  => 'nullable|string|max:50000',
            'reference'         => 'required|string|unique:produits,reference',
            'prix'              => 'required|numeric|gt:prix_achat',
            'prix_achat'        => 'required|numeric',
            'photo'             => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:10240',
            'photos.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'category_id'       => 'required|integer|exists:categories,id',
           
            'free_shipping'     => 'nullable|boolean',
            'is_new'            => 'nullable|boolean',
            'marque_id'         => 'nullable|integer|exists:marques,id',

            'avec_commission'   => 'required|boolean',
            'commission'        => 'required_if:avec_commission,true|nullable|numeric|min:0',
        ]);

        $produit = new produits();
        $produit->nom = $this->nom;
        $produit->description = $this->description;
        $produit->meta_description = $this->meta_description;
        $produit->reference = $this->reference;
        $produit->prix = $this->prix;
        $produit->prix_achat = $this->prix_achat;
     
        $produit->category_id = $this->category_id;
        $produit->marque_id = $this->marque_id ?: null;
        $produit->free_shipping = (bool)$this->free_shipping;
        $produit->is_new = (bool)$this->is_new;
        $produit->avec_commission = (bool)$this->avec_commission;
        $produit->commission = $this->avec_commission ? $this->commission : null;

        // Compression et stockage de la photo principale
        $produit->photo = $this->compressAndStoreImage($this->photo);

        // Compression et stockage de la galerie photos
        if ($this->photos) {
            $photosPaths = [];
            foreach ($this->photos as $p) {
                $photosPaths[] = $this->compressAndStoreImage($p);
            }
            $produit->photos = json_encode($photosPaths);
        } else {
            $produit->photos = json_encode([]);
        }

        $produit->save();

        $this->reset();
        session()->flash('success', 'Produit ajouté avec succès');
    }

    public function update_produit()
    {
        if ($this->produit) {
            $this->validate([
                'nom'               => 'required|string',
                'prix'              => 'required|numeric|gt:prix_achat',
                'prix_achat'        => 'required|numeric',
                'photo'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
                'photos.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
                'category_id'       => 'required|integer|exists:categories,id',
                'marque_id'         => 'nullable|integer|exists:marques,id',
                'free_shipping'     => 'nullable|boolean',
                'is_new'            => 'nullable|boolean',
                'avec_commission'   => 'required|boolean',
                'commission'        => 'required_if:avec_commission,true|nullable|numeric|min:0',
            ]);

            $this->produit->nom = $this->nom;
            $this->produit->description = $this->description;
            $this->produit->meta_description = $this->meta_description;
         
            $this->produit->prix = $this->prix;
            $this->produit->prix_achat = $this->prix_achat;
            $this->produit->category_id = $this->category_id;
            $this->produit->marque_id = $this->marque_id ?: null;
            $this->produit->free_shipping = (bool)$this->free_shipping;
            $this->produit->is_new = (bool)$this->is_new;
            $this->produit->avec_commission = (bool)$this->avec_commission;
            $this->produit->commission = $this->avec_commission ? $this->commission : null;

            // 1. Gestion de la photo principale (si une nouvelle est envoyée)
            if ($this->photo) {
                if ($this->produit->photo && Storage::disk('public')->exists($this->produit->photo)) {
                    Storage::disk('public')->delete($this->produit->photo);
                }
                $this->produit->photo = $this->compressAndStoreImage($this->photo);
            }

            // 2. Gestion de la galerie photos (si de nouvelles photos sont envoyées)
            if ($this->photos) {
                // Supprimer les anciennes photos de la galerie stockées en JSON
                $oldPhotos = json_decode($this->produit->photos, true);
                if (is_array($oldPhotos)) {
                    foreach ($oldPhotos as $oldPhoto) {
                        if (Storage::disk('public')->exists($oldPhoto)) {
                            Storage::disk('public')->delete($oldPhoto);
                        }
                    }
                }

                // Enregistrer les nouvelles
                $photosPaths = [];
                foreach ($this->photos as $p) {
                    $photosPaths[] = $this->compressAndStoreImage($p);
                }
                $this->produit->photos = json_encode($photosPaths);
            }

            $this->produit->save();

            return redirect()->route('produits')->with('success', "Produit modifié avec succès");
        }
    }
}
