<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_folder_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained('vocabulary_folders')->onDelete('cascade');
            $table->foreignId('vocabulary_id')->constrained('vocabularies')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['folder_id', 'vocabulary_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_folder_items');
    }
};
