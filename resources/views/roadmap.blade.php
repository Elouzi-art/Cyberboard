@extends('layouts.app')
@section('title', 'Roadmap')
@section('page-title', 'ROADMAP · TIMELINE')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div class="text-xs text-cyber-dim font-mono">{{ $projects->count() }} projet(s) sur la timeline</div>
        <div class="flex gap-2">
            <select id="zoom" onchange="renderRoadmap()" class="input-dark w-36">
                <option value="30">30 jours</option>
                <option value="60" selected>60 jours</option>
                <option value="90">90 jours</option>
                <option value="120">4 mois</option>
            </select>
            <a href="{{ route('projects.create') }}" class="btn-primary">+ Projet</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div id="roadmap-container" class="overflow-x-auto"></div>
    </div>

    @php $noDates = $allProjects->filter(fn($p) => !$p->start_date || !$p->end_date); @endphp
    @if($noDates->count())
    <div class="card p-4">
        <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-3">Sans dates définies</div>
        <div class="flex flex-wrap gap-2">
            @foreach($noDates as $p)
            <a href="{{ route('projects.edit', $p) }}"
               class="text-xs px-3 py-1.5 rounded border border-cyber-border hover:border-violet-500 transition text-cyber-dim hover:text-cyber-text">
                {{ $p->title }} — Ajouter des dates →
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const projects = {!! $projectsJson !!};

const TODAY = new Date();
TODAY.setHours(0,0,0,0);

function dayDiff(a, b) {
    return Math.round((b - a) / 86400000);
}

function renderRoadmap() {
    const span      = parseInt(document.getElementById('zoom').value);
    const startDate = new Date(TODAY);
    startDate.setDate(startDate.getDate() - 3);
    const endDate   = new Date(startDate);
    endDate.setDate(endDate.getDate() + span);

    const LABEL_W   = 180;
    const CELL_W    = 14;
    const totalDays = dayDiff(startDate, endDate);
    const todayOff  = dayDiff(startDate, TODAY);

    let weeks = [];
    let cur   = new Date(startDate);
    while (cur < endDate) {
        weeks.push(new Date(cur));
        cur.setDate(cur.getDate() + 7);
    }

    const totalW = LABEL_W + totalDays * CELL_W;
    let html = '<div style="min-width:' + totalW + 'px;font-family:Courier New,monospace;font-size:11px">';

    html += '<div style="display:flex;background:#111;border-bottom:1px solid #1e1e1e;position:sticky;top:0;z-index:2">';
    html += '<div style="width:' + LABEL_W + 'px;flex-shrink:0;padding:8px 12px;color:#444;text-transform:uppercase;letter-spacing:.1em;border-right:1px solid #1e1e1e">Projet</div>';
    html += '<div style="flex:1;position:relative;height:32px">';

    weeks.forEach(function(w) {
        var x = dayDiff(startDate, w) * CELL_W;
        html += '<div style="position:absolute;left:' + x + 'px;top:0;height:32px;border-left:1px solid #1e1e1e;padding:8px 4px;color:#333;white-space:nowrap">';
        html += w.toLocaleDateString('fr-FR', {day:'2-digit', month:'short'});
        html += '</div>';
    });

    html += '<div style="position:absolute;left:' + (todayOff * CELL_W) + 'px;top:0;bottom:0;width:1px;background:#e24b4a;opacity:.8"></div>';
    html += '<div style="position:absolute;left:' + (todayOff * CELL_W + 3) + 'px;top:6px;color:#e24b4a;font-size:9px">AUJ.</div>';
    html += '</div></div>';

    projects.forEach(function(p) {
        if (!p.start || !p.end) return;
        var ps = new Date(p.start); ps.setHours(0,0,0,0);
        var pe = new Date(p.end);   pe.setHours(0,0,0,0);
        var barStart = Math.max(0, dayDiff(startDate, ps));
        var barEnd   = Math.min(totalDays, dayDiff(startDate, pe));
        var barW     = Math.max(8, (barEnd - barStart) * CELL_W);
        var barX     = barStart * CELL_W;

        html += '<div style="display:flex;border-bottom:1px solid #141414;min-height:44px;align-items:stretch">';
        html += '<div style="width:' + LABEL_W + 'px;flex-shrink:0;padding:8px 12px;border-right:1px solid #1e1e1e;display:flex;flex-direction:column;justify-content:center">';
        html += '<div style="color:#c8c8c8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:' + (LABEL_W - 24) + 'px">' + p.title + '</div>';
        html += '<div style="font-size:10px;color:#444;margin-top:2px">' + p.category.toUpperCase() + '</div>';
        html += '</div>';
        html += '<div style="flex:1;position:relative;min-height:44px">';
        html += '<div style="position:absolute;left:' + (todayOff * CELL_W) + 'px;top:0;bottom:0;width:1px;background:#e24b4a;opacity:.2"></div>';

        if (barEnd > 0 && barStart < totalDays) {
            html += '<a href="' + p.url + '" style="position:absolute;left:' + barX + 'px;top:10px;width:' + barW + 'px;height:22px;border-radius:3px;background:' + p.color + ';display:flex;align-items:center;overflow:hidden;text-decoration:none">';
            html += '<div style="position:absolute;left:0;right:0;bottom:3px;height:3px;background:rgba(0,0,0,.3)"><div style="height:3px;background:rgba(255,255,255,.4);width:' + p.progress + '%"></div></div>';
            html += '<span style="font-size:10px;color:rgba(255,255,255,.9);padding:0 6px;position:relative;z-index:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + p.progress + '% · ' + p.title + '</span>';
            html += '</a>';
        }

        html += '</div></div>';
    });

    html += '</div>';
    document.getElementById('roadmap-container').innerHTML = html;
}

renderRoadmap();
</script>
@endpush
BLADEcat > resources/views/roadmap.blade.php << 'BLADE'
@extends('layouts.app')
@section('title', 'Roadmap')
@section('page-title', 'ROADMAP · TIMELINE')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div class="text-xs text-cyber-dim font-mono">{{ $projects->count() }} projet(s) sur la timeline</div>
        <div class="flex gap-2">
            <select id="zoom" onchange="renderRoadmap()" class="input-dark w-36">
                <option value="30">30 jours</option>
                <option value="60" selected>60 jours</option>
                <option value="90">90 jours</option>
                <option value="120">4 mois</option>
            </select>
            <a href="{{ route('projects.create') }}" class="btn-primary">+ Projet</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div id="roadmap-container" class="overflow-x-auto"></div>
    </div>

    @php $noDates = $allProjects->filter(fn($p) => !$p->start_date || !$p->end_date); @endphp
    @if($noDates->count())
    <div class="card p-4">
        <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-3">Sans dates définies</div>
        <div class="flex flex-wrap gap-2">
            @foreach($noDates as $p)
            <a href="{{ route('projects.edit', $p) }}"
               class="text-xs px-3 py-1.5 rounded border border-cyber-border hover:border-violet-500 transition text-cyber-dim hover:text-cyber-text">
                {{ $p->title }} — Ajouter des dates →
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const projects = {!! $projectsJson !!};

const TODAY = new Date();
TODAY.setHours(0,0,0,0);

function dayDiff(a, b) {
    return Math.round((b - a) / 86400000);
}

function renderRoadmap() {
    const span      = parseInt(document.getElementById('zoom').value);
    const startDate = new Date(TODAY);
    startDate.setDate(startDate.getDate() - 3);
    const endDate   = new Date(startDate);
    endDate.setDate(endDate.getDate() + span);

    const LABEL_W   = 180;
    const CELL_W    = 14;
    const totalDays = dayDiff(startDate, endDate);
    const todayOff  = dayDiff(startDate, TODAY);

    let weeks = [];
    let cur   = new Date(startDate);
    while (cur < endDate) {
        weeks.push(new Date(cur));
        cur.setDate(cur.getDate() + 7);
    }

    const totalW = LABEL_W + totalDays * CELL_W;
    let html = '<div style="min-width:' + totalW + 'px;font-family:Courier New,monospace;font-size:11px">';

    html += '<div style="display:flex;background:#111;border-bottom:1px solid #1e1e1e;position:sticky;top:0;z-index:2">';
    html += '<div style="width:' + LABEL_W + 'px;flex-shrink:0;padding:8px 12px;color:#444;text-transform:uppercase;letter-spacing:.1em;border-right:1px solid #1e1e1e">Projet</div>';
    html += '<div style="flex:1;position:relative;height:32px">';

    weeks.forEach(function(w) {
        var x = dayDiff(startDate, w) * CELL_W;
        html += '<div style="position:absolute;left:' + x + 'px;top:0;height:32px;border-left:1px solid #1e1e1e;padding:8px 4px;color:#333;white-space:nowrap">';
        html += w.toLocaleDateString('fr-FR', {day:'2-digit', month:'short'});
        html += '</div>';
    });

    html += '<div style="position:absolute;left:' + (todayOff * CELL_W) + 'px;top:0;bottom:0;width:1px;background:#e24b4a;opacity:.8"></div>';
    html += '<div style="position:absolute;left:' + (todayOff * CELL_W + 3) + 'px;top:6px;color:#e24b4a;font-size:9px">AUJ.</div>';
    html += '</div></div>';

    projects.forEach(function(p) {
        if (!p.start || !p.end) return;
        var ps = new Date(p.start); ps.setHours(0,0,0,0);
        var pe = new Date(p.end);   pe.setHours(0,0,0,0);
        var barStart = Math.max(0, dayDiff(startDate, ps));
        var barEnd   = Math.min(totalDays, dayDiff(startDate, pe));
        var barW     = Math.max(8, (barEnd - barStart) * CELL_W);
        var barX     = barStart * CELL_W;

        html += '<div style="display:flex;border-bottom:1px solid #141414;min-height:44px;align-items:stretch">';
        html += '<div style="width:' + LABEL_W + 'px;flex-shrink:0;padding:8px 12px;border-right:1px solid #1e1e1e;display:flex;flex-direction:column;justify-content:center">';
        html += '<div style="color:#c8c8c8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:' + (LABEL_W - 24) + 'px">' + p.title + '</div>';
        html += '<div style="font-size:10px;color:#444;margin-top:2px">' + p.category.toUpperCase() + '</div>';
        html += '</div>';
        html += '<div style="flex:1;position:relative;min-height:44px">';
        html += '<div style="position:absolute;left:' + (todayOff * CELL_W) + 'px;top:0;bottom:0;width:1px;background:#e24b4a;opacity:.2"></div>';

        if (barEnd > 0 && barStart < totalDays) {
            html += '<a href="' + p.url + '" style="position:absolute;left:' + barX + 'px;top:10px;width:' + barW + 'px;height:22px;border-radius:3px;background:' + p.color + ';display:flex;align-items:center;overflow:hidden;text-decoration:none">';
            html += '<div style="position:absolute;left:0;right:0;bottom:3px;height:3px;background:rgba(0,0,0,.3)"><div style="height:3px;background:rgba(255,255,255,.4);width:' + p.progress + '%"></div></div>';
            html += '<span style="font-size:10px;color:rgba(255,255,255,.9);padding:0 6px;position:relative;z-index:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + p.progress + '% · ' + p.title + '</span>';
            html += '</a>';
        }

        html += '</div></div>';
    });

    html += '</div>';
    document.getElementById('roadmap-container').innerHTML = html;
}

renderRoadmap();
</script>
@endpush
