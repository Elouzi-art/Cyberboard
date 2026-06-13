<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pomodoro_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('duration')->default(25);
            $table->enum('type', ['work', 'short_break', 'long_break'])->default('work');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pomodoro_sessions'); }
};
