<?php

namespace SteelAnts\LaravelAuth\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

trait HandlesEmailVerification
{
    protected function redirectIfEmailUnverified(Request $request): ?RedirectResponse
    {
        $user = $request->user();

        if ($user && method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return null;
    }

    protected function emailIsVerified(?object $user): bool
    {
        if (!$user) {
            return false;
        }

        return !method_exists($user, 'hasVerifiedEmail') || $user->hasVerifiedEmail();
    }

    public function verificationNotice(Request $request)
    {
        if ($this->emailIsVerified($request->user())) {
            return redirect()->intended($this->redirectPath());
        }

        return view('auth.verify');
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        if ($this->emailIsVerified($request->user())) {
            return redirect()->intended($this->redirectPath());
        }

        $request->fulfill();

        return redirect()->intended($this->redirectPath());
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        if ($this->emailIsVerified($request->user())) {
            return redirect()->intended($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
