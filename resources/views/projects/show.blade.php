@extends('layouts.app')
@section('title', $project->title)
@section('page-title', strtoupper($project->title))

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-start">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.index') }}" class="text-cyber-dim hover:text-white transition text-sm">← Projets</a>
            <span class="badge-{{ $project->category }}">{{ $project->category_label }}</span>
            <span class="badge-{{ $project->priority }}">{{ $project->priority_label }}</span>
            @if($project->is_pinned)<span class="text-cyber-amber text-sm">★ Épinglé</span>@endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">Modifier</a>
            <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Supprimer ce projet ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-5">

            {{-- Info card --}}
            <div class="card p-5">
                <h1 class="text-xl font-bold text-white mb-2">{{ $project->title }}</h1>
                @if($project->description)
                <p class="text-cyber-text text-sm leading-relaxed mb-4">{{ $project->description }}</p>
                @endif
                <div class="flex gap-2 flex-wrap">
                    @foreach($project->tags as $tag)
                    <span class="text-xs px-2 py-1 rounded" style="background:{{ $tag->color }}22;color:{{ $tag->color }};border:1px solid {{ $tag->color }}44">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Progression --}}
            <div class="card p-5">
                <div class="flex justify-between items-center mb-3">
                    <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider">Progression</div>
                    <span class="text-xl font-bold font-mono text-white">{{ $project->progress }}%</span>
                </div>
                <div class="bg-cyber-muted rounded-full h-3 mb-4">
                    <div id="prog-bar" class="h-3 rounded-full transition-all"
                         style="width:{{ $project->progress }}%;background:{{ $project->category_color }}"></div>
                </div>
                <input type="range" min="0" max="100" value="{{ $project->progress }}"
                       class="w-full accent-violet-500"
                       oninput="updateProgress(this.value, {{ $project->id }})">
            </div>

            {{-- Tâches --}}
            <div class="card p-5">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider">
                        Tâches ({{ $taskStats['done'] }}/{{ $taskStats['total'] }})
                    </div>
                    <button onclick="document.getElementById('new-task').classList.toggle('hidden')" class="btn-primary text-xs">+ Tâche</button>
                </div>

                {{-- Formulaire nouvelle tâche --}}
                <div id="new-task" class="hidden mb-4 p-3 bg-cyber-surface rounded-lg border border-cyber-border">
                    <form method="POST" action="{{ route('tasks.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <input type="text" name="title" required placeholder="Titre de la tâche..." class="input-dark">
                        <div class="grid grid-cols-2 gap-3">
                            <select name="priority" class="input-dark">
                                <option value="medium">Moyenne</option>
                                <option value="high">Haute</option>
                                <option value="critical">Critique</option>
                                <option value="low">Basse</option>
                            </select>
                            <input type="date" name="due_date" class="input-dark">
                        </div>
                        <textarea name="notes" placeholder="Notes..." rows="2" class="input-dark resize-none"></textarea>
                        <button type="submit" class="btn-primary w-full">Ajouter</button>
                    </form>
                </div>

                {{-- Liste des tâches --}}
                <div class="space-y-2">
                    @forelse($project->tasks as $task)
                    <div class="flex items-center gap-3 py-2 border-b border-cyber-border last:border-0">
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
                            <div class="text-sm {{ $task->status==='done' ? 'line-through text-cyber-dim' : 'text-cyber-text' }} truncate">
                                {{ $task->title }}
                            </div>
                            @if($task->notes)
                            <div class="text-xs text-cyber-dim truncate">{{ $task->notes }}</div>
                            @endif
                        </div>
                        <span class="badge-{{ $task->priority }} flex-shrink-0">{{ $task->priority_label }}</span>
                        @if($task->due_date)
                        <span class="text-xs font-mono {{ $task->is_overdue ? 'text-cyber-red' : 'text-cyber-dim' }} flex-shrink-0">
                            {{ $task->due_date->format('d/m') }}
                        </span>
                        @endif
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-cyber-dim hover:text-cyber-red text-xs transition">✕</button>
                        </form>
                    </div>
                    @empty
                    <div class="text-cyber-dim text-sm py-4 text-center">Aucune tâche — ajoutes-en une</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar droite --}}
        <div class="space-y-4">
            <div class="card p-5 space-y-3 text-sm">
                <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-2">Infos</div>
                <div class="flex justify-between">
                    <span class="text-cyber-dim">Statut</span>
                    <span class="text-cyber-text">{{ $project->status }}</span>
                </div>
                @if($project->start_date)
                <div class="flex justify-between">
                    <span class="text-cyber-dim">Début</span>
                    <span class="font-mono text-cyber-text">{{ $project->start_date->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($project->end_date)
                <div class="flex justify-between">
                    <span class="text-cyber-dim">Deadline</span>
                    <span class="font-mono {{ $project->is_overdue ? 'text-cyber-red' : 'text-cyber-text' }}">
                        {{ $project->end_date->format('d/m/Y') }}
                    </span>
                </div>
                @if($project->days_left !== null)
                <div class="flex justify-between">
                    <span class="text-cyber-dim">Temps restant</span>
                    <span class="{{ $project->is_overdue ? 'text-cyber-red' : ($project->days_left <= 7 ? 'text-cyber-amber' : 'text-cyber-green') }} font-mono">
                        {{ $project->is_overdue ? 'RETARD '.abs($project->days_left).'j' : $project->days_left.'j' }}
                    </span>
                </div>
                @endif
                @endif
                <div class="flex justify-between">
                    <span class="text-cyber-dim">Tâches</span>
                    <span class="font-mono text-cyber-text">{{ $taskStats['done'] }}/{{ $taskStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-cyber-dim">Créé le</span>
                    <span class="font-mono text-cyber-text">{{ $project->created_at->format('d/m/Y') }}</span>
                </div>
            </div>

            {{-- Compte à rebours --}}
            @if($project->end_date)
            <div class="card p-5 text-center">
                <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-3">Compte à rebours</div>
                <div class="text-4xl font-bold font-mono {{ $project->is_overdue ? 'text-cyber-red' : ($project->days_left <= 7 ? 'text-cyber-amber' : 'text-white') }}">
                    {{ abs($project->days_left) }}
                </div>
                <div class="text-xs text-cyber-dim mt-1">
                    {{ $project->is_overdue ? 'jours de retard' : 'jours restants' }}
                </div>
                <div class="bg-cyber-muted rounded-full h-1.5 mt-3">
                    <div class="h-1.5 rounded-full {{ $project->is_overdue ? 'bg-cyber-red' : 'bg-violet-500' }}"
                         style="width:{{ $project->time_progress }}%"></div>
                </div>
                <div class="text-xs text-cyber-dim font-mono mt-1">{{ $project->time_progress }}% du temps écoulé</div>
            </div>
            @endif

            <a href="{{ route('pomodoro') }}?project={{ $project->id }}"
               class="card p-4 flex items-center gap-3 hover:border-violet-700 transition block">
                <div class="text-2xl">🍅</div>
                <div>
                    <div class="text-sm text-white">Démarrer Pomodoro</div>
                    <div class="text-xs text-cyber-dim">Focus sur ce projet</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

async function toggleTask(id, btn) {
    await fetch(`/tasks/${id}/toggle`, {method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF}});
    location.reload();
}

let progTimeout;
async function updateProgress(val, projectId) {
    document.getElementById('prog-bar').style.width = val + '%';
    document.querySelector('.text-xl.font-bold').textContent = val + '%';
    clearTimeout(progTimeout);
    progTimeout = setTimeout(async () => {
        await fetch(`/projects/${projectId}/progress`, {
            method:  'PATCH',
            headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json'},
            body:    JSON.stringify({progress: parseInt(val)}),
        });
    }, 500);
}
</script>
@endpush
