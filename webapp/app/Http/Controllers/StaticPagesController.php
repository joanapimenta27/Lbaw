<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class StaticPageContoller extends Controller{
    public function about(){
        return view('pages.about');
    }
}