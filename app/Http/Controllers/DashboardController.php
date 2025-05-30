<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
        }
        $projectsCount = Project::count();

        return view('dashboard', compact('user', 'projectsCount'));
    }
}
