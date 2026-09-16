<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\register;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Region;
use App\Models\SellerQuotes;
use App\Models\State;
use App\Models\User;
use App\Models\Yayinbolgeleri;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $categories = Category::all();


        //  dd($categories);
        return view('auth.register', compact('categories'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // 'code' =>$this->ticket_number(),
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],

              


            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'min:8','string', 'confirmed', Rules\Password::defaults()],
    //   'password-confirm' => ['required', 'string', 'same:password'],
        ],[
            'email.required' => 'Veuillez entrer votre email',
            'email.email' => 'Veuillez entrer un email valide',
            'email.exists' => 'Cet email n\'existe pas',
            'email.unique' => 'Cet email existe déjà, il faut entrer un autre',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'password.string' => 'Veuillez entrer votre mot de passe',
            'password.required' => 'Veuillez entrer votre mot de passe',
            'password.confirm'=>'Le mot de passe et la confirmation doivent être identiques'
        ]
        
    );
       

        $personal_info = new User();

        $personal_info->nom = $request->nom;

        $personal_info->prenom  = $request->prenom;


        $personal_info->email = $request->email;
        $personal_info->password = Hash::make($request->password);


        $personal_info->save();



        //send mail to user
        Mail::to($personal_info->email)->send(new register($personal_info));



        event(new Registered($personal_info));


        Auth::login($personal_info);
        return redirect()->back()->with("success", "Votre compte est crée ");
    }
}
