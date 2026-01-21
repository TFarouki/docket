<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function root()
    {
        return redirect()->route('dashboard');
    }

    public function index()
    {
        return Inertia::render('Dashboard');
    }
}
