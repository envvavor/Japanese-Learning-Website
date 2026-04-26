<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quiz_sessions')) {
            Schema::create('quiz_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('category')->nullable();
                $table->string('quiz_type'); // 'multiple_choice'|'drawing'|'listening'|'mixed'
                $table->integer('total_questions');
                $table->integer('correct_answers')->default(0);
                $table->decimal('score', 5, 2)->default(0);
                $table->integer('questions_with_text_revealed')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
    }
};
