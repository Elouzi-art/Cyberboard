<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Reminder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'active_projects'  => $user->projects()->where('status', 'active')->count(),
            'total_tasks'      => $user->tasks()->where('status', '!=', 'done')->count(),
            'overdue_tasks'    => $user->tasks()->where('status', '!=', 'done')->whereDate('due_date', '<', now())->count(),
            'unread_reminders' => $user->reminders()->where('is_read', false)->where('remind_at', '<=', now())->count(),
            'avg_progress'     => (int) $user->projects()->where('status', 'active')->avg('progress'),
        ];

        $pinnedProjects = $user->projects()
            ->where('is_pinned', true)
            ->where('status', 'active')
            ->with('tags')
            ->get();

        $recentProjects = $user->projects()
            ->where('status', 'active')
            ->with('tags')
            ->orderByDesc('updated_at')
            ->take(6)
            ->get();

        $urgentTasks = $user->tasks()
            ->with('project')
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->take(8)
            ->get();

        $upcomingReminders = $user->reminders()
            ->with('project')
            ->where('is_read', false)
            ->orderBy('remind_at')
            ->take(5)
            ->get();

        $categoryStats = $user->projects()
            ->selectRaw('category, count(*) as total, avg(progress) as avg_prog')
            ->groupBy('category')
            ->get();

        return view('dashboard', compact(
            'stats', 'pinnedProjects', 'recentProjects',
            'urgentTasks', 'upcomingReminders', 'categoryStats'
        ));
    }
}
