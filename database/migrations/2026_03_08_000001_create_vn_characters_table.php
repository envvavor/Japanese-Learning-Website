<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vn_characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('default_sprite_path')->nullable();
            $table->string('elevenlabs_voice_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vn_characters');
    }
};
