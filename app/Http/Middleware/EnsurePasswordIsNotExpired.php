<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsNotExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $passwordUpdatedAt = $user->password_updated_at;

            if (! $passwordUpdatedAt || $passwordUpdatedAt->copy()->addDays(30)->isPast()) {
                if ($request->is('api/logout') || $request->is('api/users/*/password')) {
                    return $next($request);
                }

                return response()->json([
                    'message' => 'Password has expired. Please update your password.',
                    'error_code' => 'PASSWORD_EXPIRED',
                ], 403);
            }
        }

        return $next($request);
    }
}
