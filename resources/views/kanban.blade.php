@extends('layouts.app')
@section('title', 'Kanban')
@section('page-title', 'KANBAN · DRAG & DROP')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <form method="GET" action="{{ route('kanban') }}" class="flex gap-2">
            <select name="project" onchange="this.form.submit()" class="input-dark w-56">
                <option value="">Tous les projets</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ $projectId == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                @endforeach
            </select>
        </form>
        <button onclick="document.getElementById('quick-task').classList.toggle('hidden')" class="btn-primary">
            + Tâche rapide
        </button>
    </div>

    {{-- Quick add --}}
    <div id="quick-task" class="hidden card p-4">
        <form method="POST" action="{{ route('tasks.store') }}" class="flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label class="label">Titre</label>
                <input type="text" name="title" required class="input-dark" placeholder="Nouvelle tâche...">
            </div>
            <div class="w-40">
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

    {{-- Colonnes Kanban --}}
    <div class="grid grid-cols-4 gap-4">
        @php
        $colConfig = [
            'todo'        => ['label' => 'À FAIRE',   'color' => 'border-t-cyber-dim',    'dot' => 'bg-cyber-dim'],
            'in_progress' => ['label' => 'EN COURS',  'color' => 'border-t-violet-500',   'dot' => 'bg-violet-500'],
            'done'        => ['label' => 'TERMINÉ',   'color' => 'border-t-cyber-green',  'dot' => 'bg-cyber-green'],
            'cancelled'   => ['label' => 'ANNULÉ',    'color' => 'border-t-cyber-red',    'dot' => 'bg-cyber-red'],
        ];
        @endphp

        @foreach($colConfig as $status => $cfg)
        <div class="flex flex-col">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2 h-2 rounded-full {{ $cfg['dot'] }}"></div>
                <span class="text-xs font-mono text-cyber-dim tracking-wider">{{ $cfg['label'] }}</span>
                <span class="text-xs text-cyber-dim ml-auto">{{ $columns[$status]->count() }}</span>
            </div>
            <div id="col-{{ $status }}"
                 data-status="{{ $status }}"
                 class="kanban-col flex-1 min-h-32 space-y-2 p-2 rounded-lg border border-cyber-border bg-cyber-surface/30">
                @foreach($columns[$status] as $task)
                <div class="kanban-card card p-3 cursor-grab active:cursor-grabbing hover:border-violet-700 transition"
                     data-id="{{ $task->id }}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="text-sm text-cyber-text flex-1 leading-tight">{{ $task->title }}</div>
                        <span class="badge-{{ $task->priority }} ml-2 flex-shrink-0">{{ $task->priority_label }}</span>
                    </div>
                    @if($task->project)
                    <div class="text-xs text-cyber-dim mb-1">{{ $task->project->title }}</div>
                    @endif
                    @if($task->due_date)
                    <div class="text-xs font-mono {{ $task->is_overdue ? 'text-cyber-red' : 'text-cyber-dim' }}">
                        {{ $task->due_date->format('d/m/Y') }}
                    </div>
                    @endif
                    @if($task->notes)
                    <div class="text-xs text-cyber-dim mt-1 line-clamp-2">{{ $task->notes }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

document.querySelectorAll('.kanban-col').forEach(col => {
    new Sortable(col, {
        group:     'kanban',
        animation: 150,
        ghostClass:'opacity-30',
        onEnd: async function(evt) {
            const taskId = evt.item.dataset.id;
            const status = evt.to.dataset.status;
            await fetch(`/kanban/${taskId}/status`, {
                method:  'PATCH',
                headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json'},
                body:    JSON.stringify({status}),
            });
            // Mettre à jour les compteurs
            document.querySelectorAll('.kanban-col').forEach(c => {
                const count = c.querySelectorAll('.kanban-card').length;
                c.previousElementSibling.querySelector('span:last-child').textContent = count;
            });
        }
    });
});
</script>
@endpush
