<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController
{
    public static string $redirect = 'home';

    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','unique:users,name','max:255'],
            'email' => ['required','unique:users,email',],
            'password' => ['required','confirmed', Password::min(8)],
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('error', 'Sesprávné jméno nebo heslo');
    }

    public function login(): RedirectResponse
    {
        return view('auth.login'); 
    }

    public function loginPost(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if(Auth::attempt($credentials)){
            return redirect()->route($this->redirect);
        }

        return back()->with('error', 'Sesprávné jméno nebo heslo');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function reset()
    {
        return view('auth.reset');
    }

    public function resetPost(Request $request)
    {
        return view('auth.reset');
    }
}