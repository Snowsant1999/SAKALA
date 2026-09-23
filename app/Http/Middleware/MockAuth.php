<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MockAuth
{
    /**
     * Handle an incoming request.
     * Check for mock authentication via session.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is logged in
        if (!session('user_role')) {
            return redirect('/login');
        }

        // Check role if specified
        if (!empty($roles) && !in_array(session('user_role'), $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Share user data with all views
        view()->share('currentUser', session('user_data', []));
        view()->share('currentRole', session('user_role', 'guest'));

        return $next($request);
    }
}
