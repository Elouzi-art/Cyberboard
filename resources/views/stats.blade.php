@extends('layouts.app')
@section('title', 'Statistiques')
@section('page-title', 'STATISTIQUES · ANALYTICS')

@section('content')
<div class="space-y-6">

    {{-- Métriques top --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="card p-5 text-center">
            <div class="text-3xl font-bold font-mono text-violet-400">{{ $currentStreak }}</div>
            <div class="text-xs text-cyber-dim mt-1 uppercase tracking-wider">Streak actuel 🔥</div>
        </div>
        <div class="card p-5 text-center">
            <div class="text-3xl font-bold font-mono text-cyber-green">{{ $totalPomodoros }}</div>
            <div class="text-xs text-cyber-dim mt-1 uppercase tracking-wider">Pomodoros total</div>
        </div>
        <div class="card p-5 text-center">
            <div class="text-3xl font-bold font-mono text-cyber-amber">{{ $totalFocusHours }}h</div>
            <div class="text-xs text-cyber-dim mt-1 uppercase tracking-wider">Heures de focus</div>
        </div>
        <div class="card p-5 text-center">
            <div class="text-3xl font-bold font-mono text-cyber-teal">
                {{ auth()->user()->tasks()->where('status','done')->count() }}
            </div>
            <div class="text-xs text-cyber-dim mt-1 uppercase tracking-wider">Tâches terminées</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">

        {{-- Progression par catégorie --}}
        <div class="card p-5">
            <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-4">Progression par catégorie</div>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        {{-- Tâches par semaine --}}
        <div class="card p-5">
            <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-4">Tâches terminées / semaine</div>
            <div class="relative h-56">
                <canvas id="weekChart"></canvas>
            </div>
        </div>

        {{-- Priorités --}}
        <div class="card p-5">
            <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-4">Tâches par priorité</div>
            <div class="relative h-56">
                <canvas id="priorityChart"></canvas>
            </div>
        </div>

        {{-- Streak calendar --}}
        <div class="card p-5">
            <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-4">
                Activité — 30 derniers jours
                <span class="text-violet-400 ml-2">🔥 {{ $currentStreak }} jours</span>
            </div>
            <div id="streak-calendar" class="flex flex-wrap gap-1"></div>
        </div>
    </div>

    {{-- Table catégories --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-cyber-surface text-cyber-dim text-xs uppercase tracking-wider font-mono">
                <tr>
                    <th class="px-5 py-3 text-left">Catégorie</th>
                    <th class="px-5 py-3 text-left">Projets</th>
                    <th class="px-5 py-3 text-left">Progression moy.</th>
                    <th class="px-5 py-3 text-left">Terminés</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cyber-border">
                @foreach($categoryStats as $cat)
                <tr class="hover:bg-cyber-surface/50">
                    <td class="px-5 py-3"><span class="badge-{{ $cat->category }}">{{ $cat->category }}</span></td>
                    <td class="px-5 py-3 text-cyber-text font-mono">{{ $cat->total }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="bg-cyber-muted rounded-full h-1.5 w-24">
                                <div class="h-1.5 rounded-full bg-violet-500" style="width:{{ $cat->avg_progress }}%"></div>
                            </div>
                            <span class="text-cyber-dim font-mono text-xs">{{ (int)$cat->avg_progress }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-cyber-dim font-mono">{{ $cat->completed }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const COLORS = {
    cert:'#7f77dd', ctf:'#22c55e', code:'#ef9f27',
    web:'#e24b4a',  know:'#5dcaa5', other:'#888'
};

Chart.defaults.color = '#555';
Chart.defaults.borderColor = '#1e1e1e';

// Catégorie doughnut
const catData = @json($categoryStats);
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catData.map(c => c.category.toUpperCase()),
        datasets: [{
            data: catData.map(c => c.total),
            backgroundColor: catData.map(c => COLORS[c.category] || '#888'),
            borderWidth: 1,
            borderColor: '#0d0d0d',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'right', labels: { color: '#888', font: { size: 11 } } } }
    }
});

// Tâches par semaine
const weekData = @json($tasksByWeek);
new Chart(document.getElementById('weekChart'), {
    type: 'bar',
    data: {
        labels: weekData.map(w => 'S' + String(w.week).slice(-2)),
        datasets: [{
            label: 'Tâches done',
            data: weekData.map(w => w.total),
            backgroundColor: '#7f77dd88',
            borderColor: '#7f77dd',
            borderWidth: 1,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: '#1e1e1e' } },
            y: { grid: { color: '#1e1e1e' }, ticks: { stepSize: 1 } }
        }
    }
});

// Priorités
const prioData = @json($priorityStats);
const prioColors = { critical:'#e24b4a', high:'#ef9f27', medium:'#22c55e', low:'#555' };
new Chart(document.getElementById('priorityChart'), {
    type: 'bar',
    data: {
        labels: prioData.map(p => p.priority.toUpperCase()),
        datasets: [
            { label: 'Total',    data: prioData.map(p => p.total), backgroundColor: prioData.map(p => (prioColors[p.priority]||'#555')+'44'), borderColor: prioData.map(p => prioColors[p.priority]||'#555'), borderWidth: 1 },
            { label: 'Terminées', data: prioData.map(p => p.done), backgroundColor: prioData.map(p => prioColors[p.priority]||'#555'), borderWidth: 0 },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color: '#888', font: { size: 11 } } } },
        scales: {
            x: { grid: { color: '#1e1e1e' } },
            y: { grid: { color: '#1e1e1e' } }
        }
    }
});

// Streak calendar
const streakData = @json($streaks->pluck('tasks_done', 'date'));
const cal = document.getElementById('streak-calendar');
for (let i = 29; i >= 0; i--) {
    const d    = new Date();
    d.setDate(d.getDate() - i);
    const key  = d.toISOString().split('T')[0];
    const done = streakData[key] || 0;
    const div  = document.createElement('div');
    div.title  = key + ' — ' + done + ' tâche(s)';
    div.style.cssText = `width:16px;height:16px;border-radius:3px;background:${done > 0 ? '#7f77dd' : '#1e1e1e'};opacity:${done > 0 ? Math.min(1, 0.4 + done * 0.2) : 1}`;
    cal.appendChild(div);
}
</script>
@endpush
