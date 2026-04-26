<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quiz_items')) {
            Schema::create('quiz_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
                $table->string('question_type');
                $table->text('question_text');
                $table->string('correct_answer');
                $table->json('options')->nullable();
                $table->string('audio_url')->nullable();
                $table->foreignId('kanji_id')->nullable()->constrained('kanjis')->nullOnDelete();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
            return;
        }

        Schema::table('quiz_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_items', 'quiz_id')) {
                $table->foreignId('quiz_id')->after('id')->constrained('quizzes')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('quiz_items', 'question_type')) {
                $table->string('question_type')->after('quiz_id');
            }
            if (!Schema::hasColumn('quiz_items', 'question_text')) {
                $table->text('question_text')->after('question_type');
            }
            if (!Schema::hasColumn('quiz_items', 'correct_answer')) {
                $table->string('correct_answer')->after('question_text');
            }
            if (!Schema::hasColumn('quiz_items', 'options')) {
                $table->json('options')->nullable()->after('correct_answer');
            }
            if (!Schema::hasColumn('quiz_items', 'audio_url')) {
                $table->string('audio_url')->nullable()->after('options');
            }
            if (!Schema::hasColumn('quiz_items', 'kanji_id')) {
                $table->foreignId('kanji_id')->nullable()->after('audio_url')->constrained('kanjis')->nullOnDelete();
            }
            if (!Schema::hasColumn('quiz_items', 'order')) {
                $table->integer('order')->default(0)->after('kanji_id');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_items');
    }
};
