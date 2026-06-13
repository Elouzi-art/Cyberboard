<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Streak extends Model {
    protected $fillable = ['user_id','date','tasks_done','pomodoros_done'];
    protected $casts    = ['date' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
}
