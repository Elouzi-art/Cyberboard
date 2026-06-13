<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'avatar', 'bio', 'theme'];
    protected $hidden   = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function projects()  { return $this->hasMany(Project::class); }
    public function tasks()     { return $this->hasMany(Task::class); }
    public function reminders() { return $this->hasMany(Reminder::class); }
    public function tags()      { return $this->hasMany(Tag::class); }

    public function getAvatarUrlAttribute(): string {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1a1a2e&color=7f77dd&size=64';
    }

    public function getStatsAttribute(): array {
        return [
            'total_projects'   => $this->projects()->count(),
            'active_projects'  => $this->projects()->where('status', 'active')->count(),
            'total_tasks'      => $this->tasks()->count(),
            'completed_tasks'  => $this->tasks()->where('status', 'done')->count(),
            'overdue_tasks'    => $this->tasks()->where('status', '!=', 'done')->whereDate('due_date', '<', now())->count(),
            'unread_reminders' => $this->reminders()->where('is_read', false)->count(),
        ];
    }
}
