<?php

namespace SteelAnts\LaravelAuth\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\PasswordReset;

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
            'email' => ['required', 'unique:users,email'],
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
            'email' => ['required', 'email'],
        ]);

        if (method_exists($this, 'verifyResetAttempt')) {
            $this->verifyResetAttempt($request);
        }

        $status = PasswordFacade::sendResetLink($request->only('email'));
        return $status == PasswordFacade::RESET_LINK_SENT
            ? back()->with('status', trans($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => trans($status)]);
    }

    public function resetToken(Request $request, string $token)
    {
        return view('auth.reset')->with([
            'token' => $token,
            'email' => $request->input('email')
        ]);
    }

    public function resetPasswordSubmit(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $status  = PasswordFacade::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $status  == PasswordFacade::PASSWORD_RESET
            ? redirect()->route($this->redirect)->with('status', trans($status ))
            : redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($status )]);
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        //Invalidate rest of reset tokens for same user
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        Auth::guard()->login($user);
    }
}
