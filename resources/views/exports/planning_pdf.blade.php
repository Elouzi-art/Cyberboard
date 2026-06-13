<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>CyberBoard — Planning {{ now()->format('d/m/Y') }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Courier New', monospace; background: #0d0d0d; color: #c8c8c8; padding: 40px; }
h1 { color: #7f77dd; font-size: 20px; margin-bottom: 4px; }
.sub { color: #555; font-size: 12px; margin-bottom: 30px; }
.project { background: #141414; border: 1px solid #1e1e1e; border-radius: 6px; padding: 16px; margin-bottom: 16px; page-break-inside: avoid; }
.project-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
.title { color: #e8e8e8; font-size: 14px; font-weight: bold; }
.badge { font-size: 10px; padding: 2px 8px; border-radius: 3px; background: #1a1a2e; color: #7f77dd; border: 1px solid #2e2a4a; }
.bar-bg { background: #1e1e1e; border-radius: 2px; height: 4px; margin: 8px 0; }
.bar-fill { height: 4px; border-radius: 2px; background: #7f77dd; }
.meta { font-size: 11px; color: #555; display: flex; gap: 20px; }
.tasks { margin-top: 10px; padding-top: 10px; border-top: 1px solid #1e1e1e; }
.task { font-size: 11px; padding: 3px 0; color: #888; display: flex; gap: 8px; }
.task.done { text-decoration: line-through; color: #333; }
.check { color: #22c55e; }
@media print { body { background: white; color: #333; } .project { border-color: #ddd; background: #f9f9f9; } }
</style>
</head>
<body>
<h1>CYBERBOARD — Planning</h1>
<div class="sub">Exporté le {{ now()->format('d/m/Y à H:i') }} · {{ $user->name }}</div>

@foreach($projects->sortBy('end_date') as $p)
<div class="project">
    <div class="project-header">
        <div class="title">{{ $p->title }}</div>
        <span class="badge">{{ $p->category }}</span>
    </div>
    <div class="bar-bg"><div class="bar-fill" style="width:{{ $p->progress }}%"></div></div>
    <div class="meta">
        <span>Progression : {{ $p->progress }}%</span>
        <span>Priorité : {{ $p->priority }}</span>
        @if($p->start_date)<span>Début : {{ $p->start_date->format('d/m/Y') }}</span>@endif
        @if($p->end_date)<span>Fin : {{ $p->end_date->format('d/m/Y') }}</span>@endif
        <span>Statut : {{ $p->status }}</span>
    </div>
    @if($p->tasks->count())
    <div class="tasks">
        @foreach($p->tasks->take(10) as $t)
        <div class="task {{ $t->status==='done' ? 'done' : '' }}">
            <span class="check">{{ $t->status==='done' ? '✓' : '○' }}</span>
            <span>{{ $t->title }}</span>
            @if($t->due_date)<span style="color:#444">{{ $t->due_date->format('d/m') }}</span>@endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endforeach
<script>window.onload = () => window.print();</script>
</body>
</html>
