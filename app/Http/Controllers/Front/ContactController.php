<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\ContactRequest;
use App\Models\{config, Contact};

class ContactController extends Controller
{
    public function contact()
    {
        $configs= config::firstOrFail();
        return view('front.contact.contact', compact('configs'));
    }

    public function store(ContactRequest $request)
    {
        if($request->user()) {
            $request->merge([
                'user_id' => $request->user()->id,
                'name'    => $request->user()->name,
                'email'   => $request->user()->email,
            ]);
        }
        Contact::create ($request->all());
       // return back();

       return back()->with ('message', __('Votre message a été envoyé avec succès'));
      
    }

    public function about(){
        $configs= config::firstOrFail();
        return view('front.about.about', compact('configs'));
    }

}