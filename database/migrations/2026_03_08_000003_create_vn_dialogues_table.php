<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vn_dialogues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->nullable()->constrained('vn_characters')->nullOnDelete();
            $table->foreignId('background_id')->constrained('vn_backgrounds')->cascadeOnDelete();
            $table->text('original_text');
            $table->text('translated_text');
            $table->string('audio_file_path')->nullable();
            $table->foreignId('next_dialogue_id')->nullable()->constrained('vn_dialogues')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vn_dialogues');
    }
};
