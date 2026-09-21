<?php

namespace App\Livewire\categories;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddCategory extends Component
{
    use WithFileUploads;

    public $posts, $title, $body, $post_id;
    public $updateMode = false;
   

    public $nom, $photo,$photo2,$category,$description;


    public function mount($category){
        if($category){
            $this->category = $category;
            $this->nom = $category->nom;
          
           
            $this->photo2 = $category->photo;
           
        }
    }

    private function resetInputFields(){
        $this->nom = '';
        $this->description = '';
    }




    public function render()
    {
        return view('livewire.categories.add-category');
    }




    

    //validation
    public function create()
    {
        $this->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string|Max:5000',
          
        ]);
        ;[
            'description.required' => 'La description doit avoir moins de 5000 caractères',
            'nom.required' => 'Veuillez entrer le nom ',
        
            //'adresse.required' => 'Veuillez entrer votre addresse',
      
          ];

       


        $category = new Category();
        $category->nom = $this->nom;
     
        
      
      /*   if ($this->photos) {
            $photosPaths = [];
            foreach ($this->photos as $photo) {
                $photosPaths[] = $photo->store('categories', 'public');
            }
            $category->photos = json_encode($photosPaths);
        } */
        $category->save();

        //reset input
        $this->resetInput();
       // $this->resetInputFields();


        //flash message
        session()->flash('success', 'category ajoutée avec succès');
    }




    public function update_category(){
        if($this->category){
            $this->validate([
                'nom' => 'required|string',
              
              
              
             
               
            ]);



            $this->category->nom = $this->nom;
         
            


        /*     if ($this->photos) {
                $photosPaths = [];
                foreach ($this->photos as $photo) {
                    $photosPaths[] = $photo->store('categorys', 'public');
                }
                $this->category->photos = json_encode($photosPaths);
            } */
            $this->category->save();
    
  
            $this->resetInput();
    
            return redirect()->route('categories')->with('success',"category modifié avec succès");



        }
    }










    public function resetInput()
    {
        $this->nom = '';
        $this->description = '';
        $this->photo = '';
    }
}
