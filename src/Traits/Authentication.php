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
use Illuminate\Support\Facades\Route;

trait Authentication
{
    //protected string $redirect = 'home';

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

        return back()->with('success', 'Registered');
    }

    public function login(Request $request)
    {
        $url = url()->previous();
        if ($url != url()->current() && !session()->has('previous-url') && $url != route("logout")) {
            //Check if url you are redirecting to actually exists
            if (Route::getRoutes()->match(Request::create($url))){
                session(['previous-url' => $url]);
            }
        }
        return view('auth.login');
    }

    public function loginPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (method_exists($this, 'verifyLoginAttempt')) {
            if($this->verifyLoginAttempt($request)){
                return back()->with('error', 'Sesprávné jméno nebo heslo');
            }
        }

        $credentials = $validated;

        if (method_exists($this, 'loginAttempt')) {
            if ($this->loginAttempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return $this->getRegirect();
            }
        } else {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return $this->getRegirect();
            }
        }

        return back()->with('error', 'Sesprávné jméno nebo heslo');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
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
            ? redirect()->route($this->redirectPath())->with('status', trans($status))
            : redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($status)]);
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

    private function redirectPath() : string
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : 'home';
    }

    private function getRegirect(): RedirectResponse
    {
        $url = session('previous-url', route($this->redirectPath()));
        session()->forget('previous-url');
        return redirect($url);
    }

    // public function loginAttempt($credentials): bool
    // {
    //     if (true) {
    //         return true;
    //     }
    // }

    // public function loginAttempt(Request $request): bool
    // {
    //     if (true) {
    //         return true;
    //     }
    // }
}
