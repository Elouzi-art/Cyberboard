<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'category',
        'priority', 'status', 'progress', 'start_date',
        'end_date', 'color', 'is_pinned',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_pinned'  => 'boolean',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function tasks()     { return $this->hasMany(Task::class); }
    public function reminders() { return $this->hasMany(Reminder::class); }
    public function tags()      { return $this->belongsToMany(Tag::class, 'project_tag'); }

    public function getCategoryLabelAttribute(): string {
        return match($this->category) {
            'cert'  => 'Certification',
            'ctf'   => 'CTF / HTB',
            'code'  => 'Coding',
            'web'   => 'Laravel / Web',
            'know'  => 'Knowledge',
            default => 'Autre',
        };
    }

    public function getCategoryColorAttribute(): string {
        return match($this->category) {
            'cert'  => '#7f77dd',
            'ctf'   => '#22c55e',
            'code'  => '#ef9f27',
            'web'   => '#e24b4a',
            'know'  => '#5dcaa5',
            default => '#888888',
        };
    }

    public function getPriorityLabelAttribute(): string {
        return match($this->priority) {
            'critical' => 'Critique',
            'high'     => 'Haute',
            'medium'   => 'Moyenne',
            'low'      => 'Basse',
            default    => $this->priority,
        };
    }

    public function getDaysLeftAttribute(): ?int {
        if (!$this->end_date) return null;
        return now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false);
    }

    public function getIsOverdueAttribute(): bool {
        return $this->end_date && $this->end_date->isPast() && $this->status !== 'completed';
    }

    public function getTimeProgressAttribute(): int {
        if (!$this->start_date || !$this->end_date) return 0;
        $total   = $this->start_date->diffInDays($this->end_date);
        $elapsed = $this->start_date->diffInDays(now());
        if ($total <= 0) return 100;
        return min(100, max(0, (int) round($elapsed / $total * 100)));
    }
}
