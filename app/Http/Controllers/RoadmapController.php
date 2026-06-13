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

        $projectsJson = $projects->map(function($p) {
            return [
                'id'       => $p->id,
                'title'    => $p->title,
                'category' => $p->category,
                'color'    => $p->category_color,
                'progress' => $p->progress,
                'start'    => $p->start_date?->format('Y-m-d'),
                'end'      => $p->end_date?->format('Y-m-d'),
                'priority' => $p->priority,
                'url'      => route('projects.show', $p),
            ];
        })->toJson();

        return view('roadmap', compact('projects', 'allProjects', 'projectsJson'));
    }
}
