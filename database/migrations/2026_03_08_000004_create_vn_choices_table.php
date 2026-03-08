<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vn_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dialogue_id')->constrained('vn_dialogues')->cascadeOnDelete();
            $table->string('choice_text');
            $table->foreignId('target_dialogue_id')->constrained('vn_dialogues')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vn_choices');
    }
};
