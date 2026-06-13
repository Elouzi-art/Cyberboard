@extends('layouts.app')
@section('title', 'Rappels')
@section('page-title', 'RAPPELS · NOTIFICATIONS')

@section('content')
<div class="space-y-5">
    <div class="flex justify-between items-center">
        <div class="text-xs text-cyber-dim font-mono">{{ $reminders->total() }} rappel(s)</div>
        <button onclick="document.getElementById('new-reminder').classList.toggle('hidden')" class="btn-primary">
            + Nouveau rappel
        </button>
    </div>

    <div id="new-reminder" class="hidden card p-5">
        <form method="POST" action="{{ route('reminders.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Titre *</label>
                    <input type="text" name="title" required class="input-dark" placeholder="Rappel...">
                </div>
                <div>
                    <label class="label">Date & heure *</label>
                    <input type="datetime-local" name="remind_at" required class="input-dark">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Priorité</label>
                    <select name="priority" class="input-dark">
                        @foreach(['medium'=>'Moyenne','high'=>'Haute','critical'=>'Critique','low'=>'Basse'] as $v=>$l)
                        <option value="{{ $v }}">{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Projet lié</label>
                    <select name="project_id" class="input-dark">
                        <option value="">Aucun</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="label">Message</label>
                <textarea name="message" rows="2" class="input-dark resize-none" placeholder="Détails..."></textarea>
            </div>
            <button type="submit" class="btn-primary">Créer le rappel</button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($reminders as $r)
        <div class="card p-4 flex items-start gap-4
                    {{ !$r->is_read && $r->remind_at->isPast() ? 'border-red-900' : '' }}">
            <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0
                        {{ $r->is_read ? 'bg-cyber-dim' : ($r->remind_at->isPast() ? 'bg-cyber-red animate-pulse' : 'bg-cyber-green animate-pulse') }}">
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start">
                    <div class="text-sm font-medium {{ $r->is_read ? 'text-cyber-dim line-through' : 'text-white' }}">
                        {{ $r->title }}
                    </div>
                    <div class="flex items-center gap-2 ml-3">
                        <span class="badge-{{ $r->priority }}">{{ $r->priority }}</span>
                        @if(!$r->is_read)
                        <button onclick="markRead({{ $r->id }}, this)"
                                class="text-xs text-cyber-dim hover:text-cyber-green transition">✓ Lu</button>
                        @endif
                        <form method="POST" action="{{ route('reminders.destroy', $r) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-cyber-dim hover:text-cyber-red transition text-xs">✕</button>
                        </form>
                    </div>
                </div>
                @if($r->message)
                <div class="text-xs text-cyber-dim mt-1">{{ $r->message }}</div>
                @endif
                <div class="flex gap-3 mt-2 text-xs text-cyber-dim font-mono">
                    <span>{{ $r->remind_at->format('d/m/Y à H:i') }}</span>
                    @if($r->project)
                    <span>· {{ $r->project->title }}</span>
                    @endif
                    @if($r->remind_at->isPast() && !$r->is_read)
                    <span class="text-cyber-red">· EXPIRÉ</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-cyber-dim">Aucun rappel configuré.</div>
        @endforelse
    </div>
    <div>{{ $reminders->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
async function markRead(id, btn) {
    await fetch(`/reminders/${id}/read`, {method:'PATCH', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
    location.reload();
}
</script>
@endpush
