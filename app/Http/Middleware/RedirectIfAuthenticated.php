<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $role = Auth::guard($guard)->user()->role;

                $redirectTo = match ($role) {
                    'admin'         => route('admin.dashboard'),
                    'loket'         => route('loket.tiket'),
                    'tubing_mini'   => route('tubing.dashboard'),
                    'tubing_dewasa' => route('tubing.dashboard'),
                    'kolam'         => route('kolam.tiket'),
                    'kuliner'       => route('kuliner.dashboard'),
                    default         => route('login'),
                };

                return redirect($redirectTo);
            }
        }

        return $next($request);
    }
}
