<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {}

    public function boot(): void {
        Paginator::useTailwind();
        Gate::policy(\App\Models\Project::class,  \App\Policies\ProjectPolicy::class);
        Gate::policy(\App\Models\Task::class,     \App\Policies\TaskPolicy::class);
        Gate::policy(\App\Models\Reminder::class, \App\Policies\ReminderPolicy::class);
    }
}
