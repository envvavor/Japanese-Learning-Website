<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('kanji_examples', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel kanjis (pastikan nama tabel kanji Anda benar, biasanya 'kanjis')
            $table->foreignId('kanji_id')->constrained('kanjis')->onDelete('cascade'); 
            
            $table->text('japanese_text'); // Untuk TTS (Teks murni)
            $table->text('furigana_html')->nullable(); // Untuk Tampilan layar (Mengandung tag <ruby>)
            $table->string('meaning'); // Arti kalimat
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanji_examples');
    }
};
