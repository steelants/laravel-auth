<?php

namespace SteelAnts\LaravelAuth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use SteelAnts\LaravelAuth\Traits\HandlesTotp;

class EnsureTotpVerified
{
    use HandlesTotp;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $request->expectsJson() ? abort(401, 'Unauthenticated.') : redirect()->route('login');
        }

        if (!Route::has('totp.prompt')) {
            throw new LogicException('TOTP feature is disabled. Enable it via Route::auth(["totp" => true]).');
        }

        if ($response = $this->responseIfTotpRequired($request)) {
            return $response;
        }

        return $next($request);
    }
}
