<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_date')
            ->get();

        $allProjects = auth()->user()->projects()
            ->where('status', '!=', 'cancelled')
            ->get();

        return view('roadmap', compact('projects', 'allProjects'));
    }
}
