<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
                $table->integer('total_questions');
                $table->integer('correct_answers')->default(0);
                $table->decimal('score', 5, 2)->default(0);
                $table->boolean('passed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('quiz_attempts', 'quiz_id')) {
                $table->foreignId('quiz_id')->after('user_id')->constrained('quizzes')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('quiz_attempts', 'total_questions')) {
                $table->integer('total_questions')->after('quiz_id');
            }
            if (!Schema::hasColumn('quiz_attempts', 'correct_answers')) {
                $table->integer('correct_answers')->default(0)->after('total_questions');
            }
            if (!Schema::hasColumn('quiz_attempts', 'score')) {
                $table->decimal('score', 5, 2)->default(0)->after('correct_answers');
            }
            if (!Schema::hasColumn('quiz_attempts', 'passed')) {
                $table->boolean('passed')->default(false)->after('score');
            }
            if (!Schema::hasColumn('quiz_attempts', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('passed');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
