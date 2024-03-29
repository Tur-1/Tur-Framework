<?php

namespace App\Http\Controllers;

use TurFramework\Http\Request;
use App\Services\ExampleServiceInterface;

class HomeController extends Controller
{

    public function index(Request $request, ExampleServiceInterface $exampleService)
    {
        return view('pages.HomePage');
    }
}
