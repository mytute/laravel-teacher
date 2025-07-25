<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect('/login'); // Redirect if not authenticated
        }

        $user = Auth::user();

        // Check if the user's role matches one of the allowed roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action.'); // Show 403 error
        }

        return $next($request);
    }
}
