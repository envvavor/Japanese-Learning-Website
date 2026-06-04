<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_folder_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->constrained('vocabulary_folders')->onDelete('cascade');
            $table->foreignId('vocabulary_id')->constrained('vocabularies')->onDelete('cascade');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_practiced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'folder_id', 'vocabulary_id'], 'vfp_user_folder_vocab_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_folder_progress');
    }
};
