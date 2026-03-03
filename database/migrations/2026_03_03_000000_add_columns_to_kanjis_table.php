<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kanjis', function (Blueprint $table) {
            $table->enum('category', ['hiragana', 'katakana', 'kanji'])->after('strokes');
            $table->integer('level')->nullable()->after('category');
            $table->string('stroke_order_image')->nullable()->after('level');
            $table->string('kunyomi')->nullable()->after('stroke_order_image');
            $table->string('onyomi')->nullable()->after('kunyomi');
        });
    }

    public function down()
    {
        Schema::table('kanjis', function (Blueprint $table) {
            $table->dropColumn(['category', 'level', 'stroke_order_image', 'kunyomi', 'onyomi']);
        });
    }
};
