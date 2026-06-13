<?php
namespace App\Http\Controllers;

use App\Models\PomodoroSession;
use App\Models\Streak;
use Illuminate\Http\Request;

class PomodoroController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->where('status', 'active')->get();
        $tasks    = auth()->user()->tasks()->where('status', '!=', 'done')->with('project')->get();
        $today    = PomodoroSession::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->where('completed', true)
            ->count();

        return view('pomodoro', compact('projects', 'tasks', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'duration'   => 'required|integer|min:1|max:120',
            'type'       => 'required|in:work,short_break,long_break',
            'completed'  => 'required|boolean',
            'project_id' => 'nullable|exists:projects,id',
            'task_id'    => 'nullable|exists:tasks,id',
        ]);

        $session = PomodoroSession::create(array_merge($validated, ['user_id' => auth()->id()]));

        if ($validated['completed'] && $validated['type'] === 'work') {
            $streak = Streak::firstOrCreate(
                ['user_id' => auth()->id(), 'date' => today()],
                ['tasks_done' => 0, 'pomodoros_done' => 0]
            );
            $streak->increment('pomodoros_done');
        }

        return response()->json(['success' => true, 'session' => $session]);
    }
}
