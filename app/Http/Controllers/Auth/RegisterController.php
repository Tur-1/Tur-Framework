<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use TurFramework\Facades\Auth;
use TurFramework\Facades\Route;
use TurFramework\Http\Request;

class RegisterController
{
    public function index()
    {

        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:' . User::class],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);



        Auth::login($user);

        return redirect()->to(route('dashboard'))->with('success', "You're logged in!");
    }
}
