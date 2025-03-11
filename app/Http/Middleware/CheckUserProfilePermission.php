<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserProfile;

class CheckUserProfilePermission
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @return  \Symfony\Component\HttpFoundation\Response
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1 = SUPER-ADMIN 
        if ($user->role == 1) {
            return $next($request);
        }

        $profileId = $request->route("id");

        // 2 = HR 3 = PAYROLL
        if ($user->role == 2 || $user->role == 3) {

            $permissions = getUserPermissions($user);

            $user_office = UserProfile::select("office")->where("user_id", $profileId)->first();

            if ($user_office) {
                $user_office = $user_office->office;

                $matching_offices = array_intersect($permissions, [$user_office]);

                if (!empty($matching_offices)) {
                    return $next($request);
                }
            }

            abort(403, "Unauthorized - No permissions assigned.");
        }

        // RESTRICT USER TO ITS PROFILE ONLY
        if ($user->id == $profileId) {
            return $next($request);
        }

        // Get the permission passed in the route defaults (using 'permission' key)
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

        abort(403, 'Unauthorized - No permissions assigned.');
    }
}
