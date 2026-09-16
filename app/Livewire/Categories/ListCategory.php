<?php

namespace App\Livewire\categories;

use App\Models\Category;
use App\Models\produits;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class ListCategory extends Component
{

    protected $listeners = ['add-stock' => '$refresh'];
    use WithPagination;
    public $key;




    public function render()
    {
        $Query = Category::query();
        if(!is_null($this->key)){
            $Query->where('nom', 'like', '%'.$this->key.'%');
        }
        $categories = $Query->paginate(30);
        $total = Category::count();
      //  $total_supprimers = Category::onlyTrashed()->count();
       
        return view('livewire.categories.list-category', compact('categories','total'));
    }


    public function delete($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $cat->delete();
            session()->flash('info', 'Category supprimée avec succès');
        }
    }


    public function add_top($id)
    {
        $produit = produits::find($id);
        if ($produit) {
            if ($produit->top == 1) {
                $produit->top = 0;
            } else {
                $produit->top = 1;
            }
            $produit->save();
        }
    }


    public function filtrer()
    {
        //reset page
        $this->resetPage();
    }
}
