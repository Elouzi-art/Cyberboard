<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'title',
        'message', 'remind_at', 'is_read', 'priority',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_read'   => 'boolean',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function project() { return $this->belongsTo(Project::class); }

    public function getIsUrgentAttribute(): bool {
        return !$this->is_read && $this->remind_at->isPast();
    }
}
