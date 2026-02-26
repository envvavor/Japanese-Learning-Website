<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kanjis', function (Blueprint $table) {
            $table->id();
            $table->string('character')->unique(); 
            $table->string('meaning');             
            $table->json('strokes');               
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kanjis');
    }
};