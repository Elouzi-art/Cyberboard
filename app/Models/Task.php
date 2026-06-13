<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'project_id', 'title', 'notes',
        'priority', 'status', 'due_date', 'completed_at', 'order',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function project() { return $this->belongsTo(Project::class); }

    public function getIsOverdueAttribute(): bool {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
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

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'todo'        => 'À faire',
            'in_progress' => 'En cours',
            'done'        => 'Terminé',
            'cancelled'   => 'Annulé',
            default       => $this->status,
        };
    }
}
