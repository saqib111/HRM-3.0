<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function leave_form()
    {
        return view('leave_application.leave_form');
    }
}
