<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vn_dialogues', function (Blueprint $table) {
            $table->float('position_x')->default(0)->after('next_dialogue_id');
            $table->float('position_y')->default(0)->after('position_x');
        });
    }

    public function down(): void
    {
        Schema::table('vn_dialogues', function (Blueprint $table) {
            $table->dropColumn(['position_x', 'position_y']);
        });
    }
};
