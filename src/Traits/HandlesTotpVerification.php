<?php

namespace SteelAnts\LaravelAuth\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use SteelAnts\LaravelAuth\Support\Totp;

trait HandlesTotpVerification
{
    protected function totpIsSatisfied(?object $user, Request $request): bool
    {
        if (!$user) {
            return false;
        }

        $hasTotp = !empty($user->totp_secret);
        $forceTotp = (bool) ($user->totp_force ?? false);
        $totpVerified = (bool) $request->session()->get('totp_passed');

        if ($forceTotp && !$hasTotp) {
            return false;
        }

        if ($hasTotp && !$totpVerified) {
            return false;
        }

        return true;
    }

    protected function loginAttempt(array $credentials, bool $remember): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    public function totpPrompt(Request $request)
    {
        $user = $request->user();

        if (method_exists($this, 'redirectIfEmailUnverified')) {
            if ($redirect = $this->redirectIfEmailUnverified($request)) {
                return $redirect;
            }
        }

        $secret = $user->totp_secret;

        if (!$secret) {
            $secret = session('totp.secret') ?: Totp::generateSecret();
            session(['totp.secret' => $secret]);
        }

        $otpauth = Totp::otpauthUrl($user->email, config('app.name', 'Laravel'), $secret);
        $qrDataUri = Totp::buildTotpQr($otpauth);

        return view('auth.totp', [
            'otpauth' => $otpauth,
            'secret'  => $secret,
            'hasTotp' => (bool) $user->totp_secret,
            'qrDataUri' => $qrDataUri,
        ]);
    }

    public function totpVerify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (method_exists($this, 'redirectIfEmailUnverified')) {
            if ($redirect = $this->redirectIfEmailUnverified($request)) {
                return $redirect;
            }
        }

        $secret = $user->totp_secret ?? session('totp.secret');

        if (!$secret || !Totp::verify($secret, $request->string('code'))) {
            return back()->withErrors(['code' => __('Invalid TOTP code.')]);
        }

        if (!$user->totp_secret) {
            $user->forceFill([
                'totp_secret'       => $secret,
                'totp_confirmed_at' => now(),
            ])->save();
            session()->forget('totp.secret');
        } elseif (is_null($user->totp_confirmed_at)) {
            $user->forceFill([
                'totp_confirmed_at' => now(),
            ])->save();
        }

        $request->session()->put('totp_passed', true);

        return redirect()->intended($this->redirectPath());
    }
}
