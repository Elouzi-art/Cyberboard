<?php
namespace App\Http\Controllers;

use App\Models\PomodoroSession;
use App\Models\Streak;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $categoryStats = $user->projects()
            ->selectRaw('category, count(*) as total, avg(progress) as avg_progress, sum(case when status="completed" then 1 else 0 end) as completed')
            ->groupBy('category')
            ->get();

        $tasksByWeek = $user->tasks()
            ->where('status', 'done')
            ->where('completed_at', '>=', now()->subWeeks(8))
            ->selectRaw('YEARWEEK(completed_at) as week, count(*) as total')
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $priorityStats = $user->tasks()
            ->selectRaw('priority, count(*) as total, sum(case when status="done" then 1 else 0 end) as done')
            ->groupBy('priority')
            ->get();

        $pomodoroStats = PomodoroSession::where('user_id', $user->id)
            ->where('completed', true)
            ->selectRaw('DATE(created_at) as day, count(*) as sessions')
            ->groupBy('day')
            ->orderBy('day')
            ->take(30)
            ->get();

        $streaks      = Streak::where('user_id', $user->id)->orderByDesc('date')->take(30)->get();
        $currentStreak = $this->calculateStreak($user->id);

        $totalPomodoros  = PomodoroSession::where('user_id', $user->id)->where('completed', true)->count();
        $totalFocusHours = (int) ($totalPomodoros * 25 / 60);

        return view('stats', compact(
            'categoryStats', 'tasksByWeek', 'priorityStats',
            'pomodoroStats', 'streaks', 'currentStreak',
            'totalPomodoros', 'totalFocusHours'
        ));
    }

    private function calculateStreak(int $userId): int
    {
        $streak = 0;
        $date   = now()->startOfDay();
        while (true) {
            $exists = Streak::where('user_id', $userId)
                ->where('date', $date->toDateString())
                ->where('tasks_done', '>', 0)
                ->exists();
            if (!$exists) break;
            $streak++;
            $date->subDay();
        }
        return $streak;
    }
}
