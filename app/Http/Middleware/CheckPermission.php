<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current user
        $user = Auth::user();

        // If the user is a superadmin (role == 1), allow access
        if ($user->role == 1) {
            return $next($request);
        }

        // Get the required permission from the route (set using defaults)
        $requiredPermission = $request->route('permission');
        // If no permission is required, proceed with the request
        if (!$requiredPermission) {
            return $next($request);
        }

        // Fetch the user's permissions from the database (assuming it's stored as a comma-separated list)
        $userPermissions = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->value('permissions');

        // If the user has no permissions, deny access
        if (!$userPermissions) {
            abort(403, 'Unauthorized - No permissions assigned.');
        }

        // Convert the user's permissions into an array
        $permissions = explode(',', $userPermissions);

        // If the user has the required permission, allow the request to proceed
        if (in_array($requiredPermission, $permissions)) {
            return $next($request);
        }

        // If the user does not have the required permission, abort with a 403 error
        abort(403, 'Unauthorized - Permission denied.');
    }
}
