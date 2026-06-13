<?php
namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReminderController extends Controller { use AuthorizesRequests;
    public function index()
    {
        $reminders = auth()->user()->reminders()
            ->with('project')
            ->orderBy('remind_at')
            ->paginate(20);

        $projects = auth()->user()->projects()->get();

        return view('reminders.index', compact('reminders', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'nullable|string',
            'remind_at'  => 'required|date',
            'priority'   => 'required|in:critical,high,medium,low',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        auth()->user()->reminders()->create($validated);
        return back()->with('success', 'Rappel créé.');
    }

    public function markRead(Reminder $reminder)
    {
        $this->authorize('update', $reminder);
        $reminder->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function destroy(Reminder $reminder)
    {
        $this->authorize('delete', $reminder);
        $reminder->delete();
        return back()->with('success', 'Rappel supprimé.');
    }
}
