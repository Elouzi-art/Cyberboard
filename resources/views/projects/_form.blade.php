<div class="max-w-2xl mx-auto">
    @if($errors->any())
    <div class="card border-red-800 p-4 mb-5">
        <ul class="text-red-400 text-sm space-y-1">
            @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
          action="{{ $project ? route('projects.update', $project) : route('projects.store') }}"
          class="card p-6 space-y-5">
        @csrf
        @if($project) @method('PUT') @endif

        <div>
            <label class="label">Titre *</label>
            <input type="text" name="title" value="{{ old('title', $project?->title) }}" required class="input-dark">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Catégorie *</label>
                <select name="category" required class="input-dark">
                    @foreach(['cert'=>'Certification','ctf'=>'CTF / HTB','code'=>'Coding','web'=>'Laravel / Web','know'=>'Knowledge','other'=>'Autre'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('category', $project?->category)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Priorité *</label>
                <select name="priority" required class="input-dark">
                    @foreach(['critical'=>'Critique','high'=>'Haute','medium'=>'Moyenne','low'=>'Basse'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('priority', $project?->priority)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Statut *</label>
                <select name="status" required class="input-dark">
                    @foreach(['active'=>'Actif','paused'=>'Pausé','completed'=>'Terminé','cancelled'=>'Annulé'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('status', $project?->status??'active')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Progression ({{ old('progress', $project?->progress ?? 0) }}%)</label>
                <input type="range" name="progress" min="0" max="100"
                       value="{{ old('progress', $project?->progress ?? 0) }}"
                       class="w-full accent-violet-500 mt-2"
                       oninput="document.getElementById('prog-val').textContent=this.value+'%'">
                <span id="prog-val" class="text-xs text-cyber-dim">{{ old('progress', $project?->progress ?? 0) }}%</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Date de début</label>
                <input type="date" name="start_date" value="{{ old('start_date', $project?->start_date?->format('Y-m-d')) }}" class="input-dark">
            </div>
            <div>
                <label class="label">Deadline</label>
                <input type="date" name="end_date" value="{{ old('end_date', $project?->end_date?->format('Y-m-d')) }}" class="input-dark">
            </div>
        </div>

        <div>
            <label class="label">Description</label>
            <textarea name="description" rows="4" class="input-dark resize-none" placeholder="Objectif du projet...">{{ old('description', $project?->description) }}</textarea>
        </div>

        @if($tags->count())
        <div>
            <label class="label">Tags</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer transition"
                       style="border-color:{{ in_array($tag->id, old('tags',$selectedTags)) ? $tag->color : '#1e1e1e' }};background:{{ in_array($tag->id, old('tags',$selectedTags)) ? $tag->color.'22' : 'transparent' }}">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                           {{ in_array($tag->id, old('tags',$selectedTags)) ? 'checked' : '' }}
                           class="hidden">
                    <span class="text-xs" style="color:{{ $tag->color }}">{{ $tag->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_pinned" value="0">
                <input type="checkbox" name="is_pinned" value="1"
                       {{ old('is_pinned', $project?->is_pinned) ? 'checked' : '' }}
                       class="accent-violet-500">
                <span class="text-sm text-cyber-text">Épingler ce projet</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2 border-t border-cyber-border">
            <button type="submit" class="btn-primary">
                {{ $project ? 'Enregistrer' : 'Créer le projet' }}
            </button>
            <a href="{{ route('projects.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>
