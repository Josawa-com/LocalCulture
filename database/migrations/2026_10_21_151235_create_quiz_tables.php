<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quest harian & mingguan (created by admin)
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['daily', 'weekly']);
            $table->string('title');
            $table->text('description')->nullable();
            // The quiz date for daily quests / week start for weekly quests
            $table->date('available_on');
            $table->timestamp('ends_at')->nullable();
            $table->unsignedTinyInteger('time_limit')->default(7); // seconds per question
            $table->unsignedTinyInteger('max_attempts')->default(1); // no replay with same account
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Quiz questions per quest
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options'); // ['A text', 'B text', 'C text', 'D text']
            $table->unsignedTinyInteger('correct_index'); // 0-3
            $table->unsignedSmallInteger('points')->default(100);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // One attempt per user per quest - prevents replays
        Schema::create('quest_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('total_time_ms')->default(0);
            $table->unsignedTinyInteger('status')->default(0); // 0=ongoing, 1=finished
            $table->unique(['quest_id', 'user_id']); // database-level anti-replay
            $table->timestamps();
        });

        // Server-side answer log: records when question was shown & when answered
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_attempt_id')->constrained('quest_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('selected_index')->nullable(); // null = timed out
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('time_ms')->default(0); // server-measured answer time
            $table->unsignedInteger('score')->default(0);
            $table->timestamp('shown_at'); // when question was served to user
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
        });

        // User reputation/score aggregate for leaderboard
        Schema::create('reputations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('total_score')->default(0);
            $table->unsignedInteger('quests_completed')->default(0);
            $table->unsignedInteger('mysteries_solved')->default(0);
            $table->unsignedInteger('streak')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reputations');
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('quest_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quests');
    }
};
