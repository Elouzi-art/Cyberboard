<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PomodoroSession extends Model {
    protected $fillable = ['user_id','project_id','task_id','duration','type','completed'];
    protected $casts    = ['completed' => 'boolean'];
    public function user()    { return $this->belongsTo(User::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function task()    { return $this->belongsTo(Task::class); }
}
