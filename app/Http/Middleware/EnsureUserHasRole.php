<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: EnsureUserHasRole
 *
 * Checks that the authenticated user has one of the required role names.
 *
 * Usage in routes:
 *   Route::middleware('role:ADMIN')          // single role
 *   Route::middleware('role:ADMIN,VIEWER')   // either role allowed
 *
 * Behaviour:
 *   - Unauthenticated users → redirect to /login
 *   - Authenticated but wrong role → abort(403 Forbidden)
 *   - Inactive users (is_active = 'N') → abort(403 Forbidden)
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Must be logged in
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Check active status
        if ($user->is_active !== 'Y') {
            return redirect()->route('home')
                ->with('error', 'Your account has been deactivated. Please contact an administrator.');
        }

        // Load the role relationship if not already loaded
        $user->loadMissing('role');

        // Check if the user's role name matches any of the allowed roles
        if (! $user->role || ! in_array($user->role->role_name, $roles, true)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access that page. Admin access is required.');
        }

        return $next($request);
    }
}
