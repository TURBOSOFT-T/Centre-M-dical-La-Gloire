<?php

namespace App\Http\Controllers;

use App\Models\Banners;
use Illuminate\Http\Request;

class BannersController extends Controller
{
    
    public function index(){
        return view("admin.Banners.index");
    }


    public function index_update($id){
        $banner = Banners::find($id);
        if(!$banner){
            abort(404);
        }
        return view("admin.Banners.update", compact("banner"));
    }
    


}
