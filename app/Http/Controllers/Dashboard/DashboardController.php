<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display a dashboard of the resource.
     */
    public function dashboard()
    {
        return view('dashboard');
    }
}
