@extends('layouts.app')
@section('title', 'Pomodoro')
@section('page-title', 'POMODORO · FOCUS TIMER')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Timer principal --}}
    <div class="card p-8 text-center">
        <div class="flex justify-center gap-2 mb-8">
            @foreach(['work'=>'FOCUS 25min','short_break'=>'PAUSE 5min','long_break'=>'LONGUE 15min'] as $type => $label)
            <button onclick="setMode('{{ $type }}')"
                    id="mode-{{ $type }}"
                    class="text-xs px-4 py-2 rounded-lg border transition font-mono
                           {{ $type === 'work' ? 'border-violet-500 text-violet-400 bg-violet-950' : 'border-cyber-border text-cyber-dim hover:border-cyber-muted' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- SVG Timer --}}
        <div class="relative flex items-center justify-center mb-8">
            <svg class="w-56 h-56 -rotate-90" viewBox="0 0 200 200">
                <circle cx="100" cy="100" r="90" fill="none" stroke="#1e1e1e" stroke-width="8"/>
                <circle id="timer-ring" cx="100" cy="100" r="90" fill="none"
                        stroke="#7f77dd" stroke-width="8"
                        stroke-linecap="round"
                        stroke-dasharray="565.48"
                        stroke-dashoffset="0"
                        class="transition-all duration-1000"/>
            </svg>
            <div class="absolute text-center">
                <div id="timer-display" class="text-5xl font-bold font-mono text-white">25:00</div>
                <div id="timer-label" class="text-xs text-cyber-dim font-mono mt-1">FOCUS</div>
            </div>
        </div>

        {{-- Contrôles --}}
        <div class="flex justify-center gap-4 mb-6">
            <button onclick="resetTimer()"
                    class="btn-secondary w-12 h-12 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            <button id="start-btn" onclick="toggleTimer()"
                    class="btn-primary w-20 h-12 rounded-full text-sm font-mono font-bold">
                START
            </button>
            <button onclick="skipTimer()"
                    class="btn-secondary w-12 h-12 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Sessions aujourd'hui --}}
        <div class="text-cyber-dim text-xs font-mono">
            Sessions aujourd'hui : <span id="session-count" class="text-violet-400">{{ $today }}</span>
        </div>
    </div>

    {{-- Config --}}
    <div class="card p-5">
        <div class="text-xs text-cyber-dim font-mono uppercase tracking-wider mb-4">Lier à un projet / tâche</div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Projet</label>
                <select id="sel-project" class="input-dark">
                    <option value="">Aucun</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Tâche</label>
                <select id="sel-task" class="input-dark">
                    <option value="">Aucune</option>
                    @foreach($tasks as $t)
                    <option value="{{ $t->id }}" data-project="{{ $t->project_id }}">{{ $t->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tips --}}
    <div class="card p-4 text-xs text-cyber-dim font-mono space-y-1">
        <div>→ 25 min de focus · 5 min de pause · toutes les 4 sessions : 15 min</div>
        <div>→ Les sessions complètes sont enregistrées dans tes statistiques</div>
        <div>→ Le son joue à la fin de chaque session (si autorisé par le navigateur)</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

const MODES = {
    work:        { label:'FOCUS',        duration:25*60, color:'#7f77dd' },
    short_break: { label:'PAUSE COURTE', duration:5*60,  color:'#22c55e' },
    long_break:  { label:'LONGUE PAUSE', duration:15*60, color:'#5dcaa5' },
};

let currentMode = 'work';
let timeLeft    = MODES.work.duration;
let running     = false;
let interval    = null;
let sessionsDone = {{ $today }};

const ring    = document.getElementById('timer-ring');
const display = document.getElementById('timer-display');
const label   = document.getElementById('timer-label');
const btn     = document.getElementById('start-btn');
const CIRC    = 2 * Math.PI * 90;

function setMode(mode) {
    if (running) return;
    currentMode = mode;
    timeLeft    = MODES[mode].duration;
    ring.style.stroke = MODES[mode].color;
    label.textContent = MODES[mode].label;
    updateDisplay();
    updateRing(1);

    document.querySelectorAll('[id^=mode-]').forEach(b => {
        b.className = 'text-xs px-4 py-2 rounded-lg border transition font-mono border-cyber-border text-cyber-dim hover:border-cyber-muted';
    });
    const active = document.getElementById('mode-' + mode);
    active.className = 'text-xs px-4 py-2 rounded-lg border transition font-mono border-violet-500 text-violet-400 bg-violet-950';
}

function updateDisplay() {
    const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
    const s = (timeLeft % 60).toString().padStart(2, '0');
    display.textContent = m + ':' + s;
    document.title      = m + ':' + s + ' · CyberBoard';
}

function updateRing(ratio) {
    ring.style.strokeDashoffset = CIRC * (1 - ratio);
}

function toggleTimer() {
    if (running) {
        clearInterval(interval);
        running = false;
        btn.textContent = 'START';
    } else {
        running = true;
        btn.textContent = 'PAUSE';
        interval = setInterval(tick, 1000);
    }
}

function tick() {
    timeLeft--;
    const total = MODES[currentMode].duration;
    updateDisplay();
    updateRing(timeLeft / total);
    if (timeLeft <= 0) {
        clearInterval(interval);
        running = false;
        btn.textContent = 'START';
        onComplete();
    }
}

function resetTimer() {
    clearInterval(interval);
    running  = false;
    timeLeft = MODES[currentMode].duration;
    btn.textContent = 'START';
    updateDisplay();
    updateRing(1);
    document.title = 'CyberBoard';
}

function skipTimer() {
    resetTimer();
}

async function onComplete() {
    // Son de fin
    try {
        const ctx = new AudioContext();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.8);
        osc.start(); osc.stop(ctx.currentTime + 0.8);
    } catch(e) {}

    // Enregistrer
    const projId = document.getElementById('sel-project').value;
    const taskId = document.getElementById('sel-task').value;
    await fetch('/pomodoro', {
        method:  'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json'},
        body: JSON.stringify({
            duration:    MODES[currentMode].duration / 60,
            type:        currentMode,
            completed:   true,
            project_id:  projId || null,
            task_id:     taskId || null,
        })
    });

    if (currentMode === 'work') {
        sessionsDone++;
        document.getElementById('session-count').textContent = sessionsDone;
    }

    // Auto switch
    if (currentMode === 'work') {
        setMode(sessionsDone % 4 === 0 ? 'long_break' : 'short_break');
    } else {
        setMode('work');
    }
}

updateDisplay();
</script>
@endpush
