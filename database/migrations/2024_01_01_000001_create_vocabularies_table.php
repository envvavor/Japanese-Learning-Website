<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();
            $table->string('original');           // Kanji/kana asli
            $table->string('furigana')->nullable(); // Cara baca (furigana)
            $table->text('english');              // Arti bahasa Inggris
            $table->enum('jlpt_level', ['N1', 'N2', 'N3', 'N4', 'N5']); // Level JLPT
            $table->timestamps();

            // Index untuk pencarian cepat
            $table->index('jlpt_level');
            $table->index('original');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabularies');
    }
};