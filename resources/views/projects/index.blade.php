@extends('layouts.app')
@section('title', 'Projets')
@section('page-title', 'PROJETS · LISTE')

@section('content')
<div class="space-y-5">
    <div class="flex justify-between items-center">
        <div class="text-xs text-cyber-dim font-mono">{{ $projects->total() }} projet(s)</div>
        <a href="{{ route('projects.create') }}" class="btn-primary">+ Nouveau projet</a>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ route('projects.index') }}" class="flex gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Rechercher..." class="input-dark w-48">
        <select name="category" class="input-dark w-36">
            <option value="">Catégorie</option>
            @foreach(['cert'=>'Certification','ctf'=>'CTF/HTB','code'=>'Coding','web'=>'Web','know'=>'Knowledge','other'=>'Autre'] as $v=>$l)
            <option value="{{ $v }}" {{ request('category')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="priority" class="input-dark w-32">
            <option value="">Priorité</option>
            @foreach(['critical'=>'Critique','high'=>'Haute','medium'=>'Moyenne','low'=>'Basse'] as $v=>$l)
            <option value="{{ $v }}" {{ request('priority')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="status" class="input-dark w-32">
            <option value="">Statut</option>
            @foreach(['active'=>'Actif','paused'=>'Pausé','completed'=>'Terminé','cancelled'=>'Annulé'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary">Filtrer</button>
        <a href="{{ route('projects.index') }}" class="btn-secondary">Reset</a>
    </form>

    @if($projects->isEmpty())
    <div class="card p-16 text-center text-cyber-dim">
        <div class="text-4xl mb-4">🚀</div>
        <div class="text-sm mb-4">Aucun projet — commence par en créer un</div>
        <a href="{{ route('projects.create') }}" class="btn-primary">Créer mon premier projet</a>
    </div>
    @else
    <div class="grid grid-cols-3 gap-4">
        @foreach($projects as $p)
        <div class="card p-4 hover:border-violet-700 transition">
            <div class="flex justify-between items-start mb-3">
                <a href="{{ route('projects.show', $p) }}"
                   class="text-sm font-medium text-white hover:text-violet-400 transition flex-1 truncate">
                    {{ $p->title }}
                    @if($p->is_pinned) <span class="text-cyber-amber">★</span> @endif
                </a>
                <div class="flex gap-1 ml-2">
                    <a href="{{ route('projects.edit', $p) }}" class="text-cyber-dim hover:text-white text-xs transition">✎</a>
                    <form method="POST" action="{{ route('projects.destroy', $p) }}" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-cyber-dim hover:text-cyber-red text-xs transition ml-1">✕</button>
                    </form>
                </div>
            </div>
            <div class="flex gap-1 flex-wrap mb-3">
                <span class="badge-{{ $p->category }}">{{ $p->category_label }}</span>
                <span class="badge-{{ $p->priority }}">{{ $p->priority_label }}</span>
                @foreach($p->tags->take(2) as $tag)
                <span class="text-xs px-1.5 py-0.5 rounded" style="background:{{ $tag->color }}22;color:{{ $tag->color }};border:1px solid {{ $tag->color }}44">{{ $tag->name }}</span>
                @endforeach
            </div>
            <div class="bg-cyber-muted rounded-full h-1.5 mb-1">
                <div class="h-1.5 rounded-full transition-all" style="width:{{ $p->progress }}%;background:{{ $p->category_color }}"></div>
            </div>
            <div class="flex justify-between text-xs text-cyber-dim font-mono">
                <span>{{ $p->progress }}%</span>
                @if($p->end_date)
                <span class="{{ $p->is_overdue ? 'text-cyber-red' : ($p->days_left <= 7 ? 'text-cyber-amber' : '') }}">
                    {{ $p->end_date->format('d/m/Y') }}
                    @if($p->is_overdue) · RETARD @elseif($p->days_left <= 7) · {{ $p->days_left }}j @endif
                </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $projects->links() }}</div>
    @endif
</div>
@endsection
