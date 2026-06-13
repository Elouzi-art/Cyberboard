@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'DASHBOARD · CYBER MISSION CONTROL')

@section('content')
<div class="space-y-6">

{{-- MÉTRIQUES --}}
<div class="grid grid-cols-5 gap-4">
    @foreach([
        ['label'=>'PROJETS ACTIFS',   'value'=>$stats['active_projects'],  'color'=>'text-violet-400'],
        ['label'=>'TÂCHES RESTANTES', 'value'=>$stats['total_tasks'],      'color'=>'text-cyber-green'],
        ['label'=>'EN RETARD',        'value'=>$stats['overdue_tasks'],    'color'=>'text-cyber-red'],
        ['label'=>'RAPPELS',          'value'=>$stats['unread_reminders'], 'color'=>'text-cyber-amber'],
        ['label'=>'PROGRESSION MOY.', 'value'=>$stats['avg_progress'].'%','color'=>'text-cyber-teal'],
    ] as $m)
    <div class="card p-4">
        <div class="text-xs text-cyber-dim font-mono tracking-wider mb-2">{{ $m['label'] }}</div>
        <div class="text-3xl font-bold font-mono {{ $m['color'] }}">{{ $m['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- PROJETS ÉPINGLÉS --}}
@if($pinnedProjects->count())
<div>
    <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-3">
        ★ Projets épinglés
    </div>
    <div class="grid grid-cols-3 gap-4">
        @foreach($pinnedProjects as $p)
        <a href="{{ route('projects.show', $p) }}"
           class="card p-4 hover:border-violet-700 transition block">
            <div class="flex justify-between items-start mb-3">
                <div class="font-medium text-sm text-white truncate flex-1">{{ $p->title }}</div>
                <span class="badge-{{ $p->category }} ml-2 flex-shrink-0">{{ $p->category_label }}</span>
            </div>
            <div class="flex justify-between text-xs text-cyber-dim mb-2">
                <span>{{ $p->progress }}%</span>
                @if($p->end_date)
                <span class="{{ $p->is_overdue ? 'text-cyber-red' : ($p->days_left <= 7 ? 'text-cyber-amber' : '') }}">
                    {{ $p->is_overdue ? 'RETARD '.(abs($p->days_left)).'j' : $p->days_left.'j restants' }}
                </span>
                @endif
            </div>
            <div class="bg-cyber-muted rounded-full h-1.5 w-full">
                <div class="h-1.5 rounded-full transition-all"
                     style="width:{{ $p->progress }}%;background:{{ $p->category_color }}"></div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

<div class="grid grid-cols-3 gap-6">

    {{-- TÂCHES URGENTES --}}
    <div class="col-span-2 card p-5">
        <div class="flex justify-between items-center mb-4">
            <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider">Tâches urgentes</div>
            <a href="{{ route('tasks.index') }}" class="text-xs text-violet-400 hover:text-violet-300">Voir tout →</a>
        </div>
        <div class="space-y-2">
            @forelse($urgentTasks as $task)
            <div class="flex items-center gap-3 py-2 border-b border-cyber-border last:border-0">
                <button onclick="toggleTask({{ $task->id }}, this)"
                        class="w-4 h-4 rounded border flex-shrink-0 flex items-center justify-center transition
                               {{ $task->status === 'done' ? 'bg-emerald-900 border-emerald-600' : 'border-cyber-muted hover:border-violet-500' }}">
                    @if($task->status === 'done')
                    <svg class="w-2.5 h-2.5 text-cyber-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                    @endif
                </button>
                <div class="flex-1 min-w-0">
                    <div class="text-sm {{ $task->status === 'done' ? 'line-through text-cyber-dim' : 'text-cyber-text' }} truncate">
                        {{ $task->title }}
                    </div>
                    @if($task->project)
                    <div class="text-xs text-cyber-dim truncate">{{ $task->project->title }}</div>
                    @endif
                </div>
                <span class="badge-{{ $task->priority }} flex-shrink-0">{{ $task->priority_label }}</span>
                @if($task->due_date)
                <span class="text-xs font-mono flex-shrink-0 {{ $task->is_overdue ? 'text-cyber-red' : 'text-cyber-dim' }}">
                    {{ $task->due_date->format('d/m') }}
                </span>
                @endif
            </div>
            @empty
            <div class="text-cyber-dim text-sm py-4 text-center">Aucune tâche urgente 🎉</div>
            @endforelse
        </div>
    </div>

    {{-- RAPPELS + STATS --}}
    <div class="space-y-4">
        <div class="card p-5">
            <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-4">Rappels à venir</div>
            <div class="space-y-3">
                @forelse($upcomingReminders as $r)
                <div class="flex items-start gap-2 py-1 border-b border-cyber-border last:border-0">
                    <div class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0
                                {{ $r->is_urgent ? 'bg-cyber-red animate-pulse' : 'bg-cyber-dim' }}"></div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-cyber-text truncate">{{ $r->title }}</div>
                        <div class="text-xs text-cyber-dim">{{ $r->remind_at->format('d/m H:i') }}</div>
                    </div>
                    <button onclick="markRead({{ $r->id }}, this)" class="text-cyber-dim hover:text-cyber-green text-xs">✓</button>
                </div>
                @empty
                <div class="text-cyber-dim text-xs text-center py-2">Aucun rappel</div>
                @endforelse
            </div>
        </div>

        {{-- Cat stats mini --}}
        <div class="card p-5">
            <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-4">Par catégorie</div>
            <div class="space-y-2">
                @foreach($categoryStats as $cat)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="badge-{{ $cat->category }}">{{ $cat->category }}</span>
                        <span class="text-cyber-dim">{{ $cat->total }} · {{ (int)$cat->avg_progress }}%</span>
                    </div>
                    <div class="bg-cyber-muted rounded-full h-1">
                        <div class="h-1 rounded-full" style="width:{{ $cat->avg_progress }}%;background:var(--color-{{ $cat->category }}, #7f77dd)"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- PROJETS RÉCENTS --}}
<div>
    <div class="flex justify-between items-center mb-3">
        <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider">Projets récents</div>
        <a href="{{ route('projects.create') }}" class="btn-primary text-xs">+ Nouveau projet</a>
    </div>
    <div class="grid grid-cols-3 gap-4">
        @foreach($recentProjects->take(6) as $p)
        <a href="{{ route('projects.show', $p) }}" class="card p-4 hover:border-violet-700 transition block">
            <div class="flex justify-between items-start mb-2">
                <div class="text-sm font-medium text-white truncate flex-1">{{ $p->title }}</div>
                <span class="badge-{{ $p->priority }} ml-2">{{ $p->priority_label }}</span>
            </div>
            <div class="flex gap-1 flex-wrap mb-3">
                <span class="badge-{{ $p->category }}">{{ $p->category_label }}</span>
                @foreach($p->tags->take(2) as $tag)
                <span class="text-xs px-1.5 py-0.5 rounded" style="background:{{ $tag->color }}22;color:{{ $tag->color }};border:1px solid {{ $tag->color }}44">{{ $tag->name }}</span>
                @endforeach
            </div>
            <div class="bg-cyber-muted rounded-full h-1.5 w-full">
                <div class="h-1.5 rounded-full" style="width:{{ $p->progress }}%;background:{{ $p->category_color }}"></div>
            </div>
            <div class="flex justify-between text-xs text-cyber-dim mt-1">
                <span>{{ $p->progress }}%</span>
                @if($p->end_date)<span>{{ $p->end_date->format('d/m/Y') }}</span>@endif
            </div>
        </a>
        @endforeach
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
async function toggleTask(id, btn) {
    const res  = await fetch(`/tasks/${id}/toggle`, {method:'PATCH', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
    const data = await res.json();
    location.reload();
}

async function markRead(id, btn) {
    await fetch(`/reminders/${id}/read`, {method:'PATCH', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
    btn.closest('.flex').remove();
}
</script>
@endpush
