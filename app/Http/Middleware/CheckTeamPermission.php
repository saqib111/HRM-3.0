<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserProfile;

class CheckTeamPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $employeeId = $request->route('id'); // Assuming the 'id' parameter is passed in the route

        // Allow Superadmin unrestricted access
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

        // Check if the user is a team leader and has access to this employee
        $isTeamMember = DB::table('leader_employees')
            ->where('leader_id', $user->id) // Check if the leader_id matches the authenticated user
            ->where('employee_id', $employeeId) // Check if the employee_id matches the requested employee
            ->exists();

        if ($isTeamMember) {
            return $next($request); // Allow access if the employee belongs to the leader's team
        }

        // Abort with a 403 Forbidden status if the user doesn't have access
        abort(403, 'Unauthorized - No permissions assigned.');
    }
}
