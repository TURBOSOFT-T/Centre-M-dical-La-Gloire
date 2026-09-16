<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{

    public $email,$code,$enter_code,$password,$password_confirmation;
    public $step = 1,$user;
    public $end = false;


    public function showForgetPasswordForm(){
        return view('front.froget');
    }

    
    

}
