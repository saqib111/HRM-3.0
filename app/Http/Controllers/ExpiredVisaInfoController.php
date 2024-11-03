<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;


class ExpiredVisaInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('username')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->join('visa_infos', 'users.id', '=', 'visa_infos.user_id')
                ->select('username', 'departments.name as department_name', 'visa_infos.visa_no', 'visa_infos.v_expiry_date')
                ->get()
                ->map(function ($item) {
                    // Calculate total days from now to expiry date
                    $expiryDate = \Carbon\Carbon::parse($item->v_expiry_date);
                    $currentDate = now();

                    // Calculate remaining days
                    if ($expiryDate->isPast()) {
                        $item->remaining_days = 'Expired'; // Just the text
                    } elseif ($expiryDate->isToday()) {
                        $item->remaining_days = 0;
                    } else {
                        $remainingDays = $currentDate->diffInDays($expiryDate);
                        $item->remaining_days = round($remainingDays) . ' Days'; // Round the result
                    }

                    return $item;
                });

            return DataTables::of($data)->make(true); // Return DataTables response
        }

        return view('expiredDocuments.expired-visa-information');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}