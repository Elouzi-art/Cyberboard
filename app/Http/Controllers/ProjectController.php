<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller { use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = auth()->user()->projects()->with('tags');

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('priority'))  $query->where('priority', $request->priority);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('search'))    $query->where('title', 'like', '%' . $request->search . '%');

        $sort = $request->get('sort', 'updated_at');
        $query->orderByDesc($sort === 'deadline' ? 'end_date' : $sort);

        $projects = $query->paginate(12)->withQueryString();
        $tags     = auth()->user()->tags()->get();

        return view('projects.index', compact('projects', 'tags'));
    }

    public function create()
    {
        $tags = auth()->user()->tags()->get();
        return view('projects.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:cert,ctf,code,web,know,other',
            'priority'    => 'required|in:critical,high,medium,low',
            'status'      => 'required|in:active,paused,completed,cancelled',
            'progress'    => 'integer|min:0|max:100',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string',
            'is_pinned'   => 'boolean',
            'tags'        => 'nullable|array',
        ]);

        $project = auth()->user()->projects()->create($validated);

        if ($request->filled('tags')) {
            $project->tags()->sync($request->tags);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['tasks' => fn($q) => $q->orderBy('order'), 'tags', 'reminders']);

        $taskStats = [
            'total'       => $project->tasks->count(),
            'done'        => $project->tasks->where('status', 'done')->count(),
            'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
            'todo'        => $project->tasks->where('status', 'todo')->count(),
        ];

        return view('projects.show', compact('project', 'taskStats'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $tags         = auth()->user()->tags()->get();
        $selectedTags = $project->tags->pluck('id')->toArray();
        return view('projects.edit', compact('project', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:cert,ctf,code,web,know,other',
            'priority'    => 'required|in:critical,high,medium,low',
            'status'      => 'required|in:active,paused,completed,cancelled',
            'progress'    => 'integer|min:0|max:100',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'color'       => 'nullable|string',
            'is_pinned'   => 'boolean',
            'tags'        => 'nullable|array',
        ]);

        $project->update($validated);
        $project->tags()->sync($request->tags ?? []);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé.');
    }

    public function updateProgress(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate(['progress' => 'required|integer|min:0|max:100']);
        $project->update(['progress' => $request->progress]);
        return response()->json(['progress' => $project->progress]);
    }
}
