<?php

namespace SteelAnts\LaravelAuth\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as PasswordFacade;

trait Authentication
{
    public string $redirect = 'home';

    public function register()
    {
        return view('auth.registration');
    }

    public function registerPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('error', 'Sesprávné jméno nebo heslo');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'exists:users,email'],
            'password' => ['required'],
        ]);
        
        if (method_exists($this, 'verifyLoginAttempt')) {
            $this->verifyLoginAttempt($request);
        }

        $credentials = $validated;
        if (Auth::attempt($credentials)) {
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
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if (method_exists($this, 'verifyResetAttempt')) {
            $this->verifyResetAttempt($request);
        }

        $response = PasswordFacade::broker()->sendResetLink($request->only('email'));
        return $response == PasswordFacade::RESET_LINK_SENT
            ? back()->with('status', trans($response))
            : back() ->withInput($request->only('email'))->withErrors(['email' => trans($response)]);
    }

    public function resetToken(Request $request)
    {
        $token = $request->route()->parameter('token');
        return view('auth.reset')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }
}
