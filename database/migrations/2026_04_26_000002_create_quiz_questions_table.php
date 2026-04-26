<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quiz_questions')) {
            Schema::create('quiz_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('session_id')->constrained('quiz_sessions')->cascadeOnDelete();
                $table->foreignId('kanji_id')->constrained('kanjis')->cascadeOnDelete();
                $table->string('question_type'); // 'multiple_choice'|'drawing'|'listening'
                $table->string('question_subtype')->nullable(); // for listening subtypes
                $table->text('question_text');
                $table->string('correct_answer');
                $table->json('options')->nullable();
                $table->string('user_answer')->nullable();
                $table->boolean('is_correct')->nullable();
                $table->decimal('accuracy_score', 5, 2)->nullable();
                $table->integer('time_taken_seconds')->nullable();
                $table->string('audio_url')->nullable();
                $table->boolean('text_was_revealed')->default(false);
                $table->boolean('hint_was_used')->default(false);
                $table->integer('points_earned')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
