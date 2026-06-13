@extends('layouts.app')
@section('title', 'Tâches')
@section('page-title', 'TÂCHES · LISTE')

@section('content')
<div class="space-y-5">
    <div class="flex justify-between items-center">
        <div class="text-xs text-cyber-dim font-mono">{{ $tasks->total() }} tâche(s)</div>
        <button onclick="document.getElementById('new-task').classList.toggle('hidden')" class="btn-primary">
            + Nouvelle tâche
        </button>
    </div>

    {{-- Quick add --}}
    <div id="new-task" class="hidden card p-4">
        <form method="POST" action="{{ route('tasks.store') }}" class="flex gap-3 flex-wrap items-end">
            @csrf
            <div class="flex-1 min-w-48">
                <label class="label">Titre</label>
                <input type="text" name="title" required class="input-dark" placeholder="Nouvelle tâche...">
            </div>
            <div class="w-48">
                <label class="label">Projet</label>
                <select name="project_id" class="input-dark">
                    <option value="">Aucun</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="label">Priorité</label>
                <select name="priority" class="input-dark">
                    <option value="medium">Moyenne</option>
                    <option value="high">Haute</option>
                    <option value="critical">Critique</option>
                    <option value="low">Basse</option>
                </select>
            </div>
            <div class="w-36">
                <label class="label">Deadline</label>
                <input type="date" name="due_date" class="input-dark">
            </div>
            <button type="submit" class="btn-primary">Ajouter</button>
        </form>
    </div>

    {{-- Filtres rapides --}}
    <div class="flex gap-2 flex-wrap">
        @foreach([
            'all'    => 'Toutes',
            'overdue'=> 'En retard',
            'today'  => 'Aujourd\'hui',
            'week'   => 'Cette semaine',
        ] as $f => $l)
        <a href="{{ route('tasks.index', array_merge(request()->except('filter','page'), ['filter'=>$f])) }}"
           class="text-xs px-3 py-1.5 rounded-lg border transition font-mono
                  {{ request('filter',$f==='all'?'all':'') === $f ? 'border-violet-500 text-violet-400 bg-violet-950' : 'border-cyber-border text-cyber-dim hover:border-cyber-muted' }}">
            {{ $l }}
        </a>
        @endforeach

        <form method="GET" action="{{ route('tasks.index') }}" class="flex gap-2 ml-auto">
            <select name="status" onchange="this.form.submit()" class="input-dark w-32">
                <option value="">Statut</option>
                @foreach(['todo'=>'À faire','in_progress'=>'En cours','done'=>'Terminé','cancelled'=>'Annulé'] as $v=>$l)
                <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="priority" onchange="this.form.submit()" class="input-dark w-32">
                <option value="">Priorité</option>
                @foreach(['critical'=>'Critique','high'=>'Haute','medium'=>'Moyenne','low'=>'Basse'] as $v=>$l)
                <option value="{{ $v }}" {{ request('priority')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Liste --}}
    <div class="card overflow-hidden">
        @forelse($tasks as $task)
        <div class="flex items-center gap-4 px-5 py-3 border-b border-cyber-border last:border-0 hover:bg-cyber-surface/40 transition">
            <button onclick="toggleTask({{ $task->id }}, this)"
                    class="w-4 h-4 rounded border flex-shrink-0 flex items-center justify-center transition
                           {{ $task->status==='done' ? 'bg-emerald-900 border-emerald-600' : 'border-cyber-muted hover:border-violet-500' }}">
                @if($task->status==='done')
                <svg class="w-2.5 h-2.5 text-cyber-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                @endif
            </button>
            <div class="flex-1 min-w-0">
                <div class="text-sm {{ $task->status==='done' ? 'line-through text-cyber-dim' : 'text-cyber-text' }} truncate">{{ $task->title }}</div>
                @if($task->project)
                <div class="text-xs text-cyber-dim truncate">{{ $task->project->title }}</div>
                @endif
                @if($task->notes)
                <div class="text-xs text-cyber-dim truncate mt-0.5">{{ $task->notes }}</div>
                @endif
            </div>
            <span class="badge-{{ $task->priority }} flex-shrink-0">{{ $task->priority_label }}</span>
            <span class="text-xs px-2 py-0.5 rounded border flex-shrink-0
                {{ $task->status==='done' ? 'border-emerald-800 text-emerald-400' :
                   ($task->status==='in_progress' ? 'border-violet-800 text-violet-400' :
                   ($task->status==='cancelled' ? 'border-red-900 text-red-400' : 'border-cyber-border text-cyber-dim')) }}">
                {{ $task->status_label }}
            </span>
            @if($task->due_date)
            <span class="text-xs font-mono flex-shrink-0 {{ $task->is_overdue ? 'text-cyber-red' : 'text-cyber-dim' }}">
                {{ $task->due_date->format('d/m') }}
            </span>
            @endif
            <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                @csrf @method('DELETE')
                <button type="submit" class="text-cyber-dim hover:text-cyber-red transition text-xs">✕</button>
            </form>
        </div>
        @empty
        <div class="px-5 py-12 text-center text-cyber-dim text-sm">Aucune tâche pour ce filtre.</div>
        @endforelse
    </div>

    <div>{{ $tasks->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
async function toggleTask(id, btn) {
    await fetch(`/tasks/${id}/toggle`, {method:'PATCH', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
    location.reload();
}
</script>
@endpush
