<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
