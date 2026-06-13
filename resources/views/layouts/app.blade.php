<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CyberBoard') — Cyber Mission Control</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @stack('styles')
</head>
<body class="bg-cyber-bg text-cyber-text font-sans antialiased overflow-x-hidden">

<div class="flex min-h-screen">

    {{-- ── SIDEBAR ── --}}
    <aside class="w-48 bg-cyber-surface border-r border-cyber-border flex flex-col fixed top-0 left-0 h-screen z-40">
        <div class="px-4 py-4 border-b border-cyber-border">
            <div class="text-white font-bold text-sm tracking-widest font-mono">CYBERBOARD</div>
            <div class="text-cyber-dim text-xs tracking-wider mt-0.5">MISSION CONTROL</div>
        </div>

        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            <div class="text-xs text-cyber-dim uppercase tracking-widest px-2 mb-2">Navigation</div>
            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('projects.index') }}"
               class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                Projets
            </a>
            <a href="{{ route('tasks.index') }}"
               class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Tâches
            </a>
            <a href="{{ route('kanban') }}"
               class="nav-item {{ request()->routeIs('kanban*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                Kanban
            </a>
            <a href="{{ route('roadmap') }}"
               class="nav-item {{ request()->routeIs('roadmap') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Roadmap
            </a>
            <a href="{{ route('reminders.index') }}"
               class="nav-item {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Rappels
                @php $unread = auth()->user()->reminders()->where('is_read',false)->where('remind_at','<=',now())->count(); @endphp
                @if($unread > 0)
                <span class="ml-auto bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $unread }}</span>
                @endif
            </a>

            <div class="text-xs text-cyber-dim uppercase tracking-widest px-2 mb-2 mt-4">Outils</div>
            <a href="{{ route('pomodoro') }}"
               class="nav-item {{ request()->routeIs('pomodoro') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pomodoro
            </a>
            <a href="{{ route('stats') }}"
               class="nav-item {{ request()->routeIs('stats') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Statistiques
            </a>

            <div class="text-xs text-cyber-dim uppercase tracking-widest px-2 mb-2 mt-4">Export</div>
            <a href="{{ route('export.csv') }}" class="nav-item">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <a href="{{ route('export.pdf') }}" target="_blank" class="nav-item">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
        </nav>

        {{-- User --}}
        <div class="px-3 py-3 border-t border-cyber-border">
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-full" alt="">
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-white truncate">{{ auth()->user()->name }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-cyber-dim hover:text-red-400 transition w-full text-left">
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <div class="flex-1 ml-48 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header class="bg-cyber-surface border-b border-cyber-border px-6 py-3 flex justify-between items-center sticky top-0 z-30">
            <div class="text-xs text-cyber-dim font-mono tracking-wider">
                @yield('page-title', 'DASHBOARD')
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-cyber-green animate-pulse"></div>
                    <span class="text-xs text-cyber-dim font-mono" id="clock"></span>
                </div>
            </div>
        </header>

        {{-- Flash --}}
        @if(session('success'))
        <div class="bg-emerald-950 border-b border-emerald-800 text-emerald-400 text-xs px-6 py-2 font-mono">
            ✓ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-950 border-b border-red-800 text-red-400 text-xs px-6 py-2 font-mono">
            ✗ {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
setInterval(updateClock, 1000);
updateClock();
</script>
@stack('scripts')
</body>
</html>
