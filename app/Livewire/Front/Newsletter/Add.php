<?php

namespace App\Livewire\Front\Newsletter;

use App\Mail\New_Newsletter;
use App\Models\Newsletters;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Add extends Component
{

    public $email ,$end=false;

    
    public function render()
    {
        return view('livewire.front.newsletter.add');
    }


    public function save(){
        $this->validate([
            'email' =>'required|email|unique:Newsletters',
        ],[
            'email.required' => 'Veuillez entrer votre email',
            'email.email' => 'Veuillez entrer un email valide',
            'email.unique' => 'Votre email est déjà inscrit à la newsletter',
        ]);

        //generate token
        $token = md5(uniqid());

        //save newsletter
        $data = Newsletters::create([
            'email' => $this->email,
            'token' => $token,
        ]);

        //send email
        Mail::to($this->email)->send(new New_Newsletter($data));

        //clear input
        $this->email = '';
        $this->end = true;
        return;
    }
}
