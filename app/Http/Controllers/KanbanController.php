<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KanbanController extends Controller { use AuthorizesRequests;
    public function index(Request $request)
    {
        $projectId = $request->get('project');
        $query     = auth()->user()->tasks()->with('project');

        if ($projectId) $query->where('project_id', $projectId);

        $tasks    = $query->get();
        $projects = auth()->user()->projects()->where('status', 'active')->get();

        $columns = [
            'todo'        => $tasks->where('status', 'todo')->sortBy('order')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->sortBy('order')->values(),
            'done'        => $tasks->where('status', 'done')->sortBy('order')->values(),
            'cancelled'   => $tasks->where('status', 'cancelled')->sortBy('order')->values(),
        ];

        return view('kanban', compact('columns', 'projects', 'projectId'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $request->validate(['status' => 'required|in:todo,in_progress,done,cancelled']);
        $task->update([
            'status'       => $request->status,
            'completed_at' => $request->status === 'done' ? now() : null,
        ]);

        if ($request->status === 'done') {
            $streak = \App\Models\Streak::firstOrCreate(
                ['user_id' => auth()->id(), 'date' => today()],
                ['tasks_done' => 0, 'pomodoros_done' => 0]
            );
            $streak->increment('tasks_done');
        }

        return response()->json(['success' => true]);
    }
}
