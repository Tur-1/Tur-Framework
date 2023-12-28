<?php

namespace App\Http\Controllers;

use TurFramework\Core\Http\Request;

class HomeController
{
    public function index(Request $request)
    {

        return view('pages.HomePage');
    }

    public function about()
    {
        return view('pages.aboutPage');
    }
}
