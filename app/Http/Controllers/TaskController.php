<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller { use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = auth()->user()->tasks()->with('project');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('project'))  $query->where('project_id', $request->project);
        if ($request->filled('search'))   $query->where('title', 'like', '%' . $request->search . '%');

        if ($request->get('filter') === 'overdue') {
            $query->where('status', '!=', 'done')->whereDate('due_date', '<', now());
        } elseif ($request->get('filter') === 'today') {
            $query->whereDate('due_date', today());
        } elseif ($request->get('filter') === 'week') {
            $query->whereBetween('due_date', [today(), today()->addWeek()]);
        }

        $tasks    = $query->orderBy('due_date')->paginate(20)->withQueryString();
        $projects = auth()->user()->projects()->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'notes'      => 'nullable|string',
            'priority'   => 'required|in:critical,high,medium,low',
            'project_id' => 'nullable|exists:projects,id',
            'due_date'   => 'nullable|date',
        ]);

        auth()->user()->tasks()->create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Tâche ajoutée.');
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task);
        $isDone = $task->status === 'done';
        $task->update([
            'status'       => $isDone ? 'todo' : 'done',
            'completed_at' => $isDone ? null : now(),
        ]);
        return response()->json(['status' => $task->status]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return back()->with('success', 'Tâche supprimée.');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'notes'    => 'nullable|string',
            'priority' => 'required|in:critical,high,medium,low',
            'due_date' => 'nullable|date',
            'status'   => 'required|in:todo,in_progress,done,cancelled',
        ]);
        $task->update($validated);
        return back()->with('success', 'Tâche mise à jour.');
    }
}
