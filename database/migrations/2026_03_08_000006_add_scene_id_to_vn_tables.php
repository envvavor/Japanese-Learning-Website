<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vn_characters', function (Blueprint $table) {
            $table->foreignId('scene_id')->nullable()->after('id')->constrained('vn_scenes')->cascadeOnDelete();
        });

        Schema::table('vn_backgrounds', function (Blueprint $table) {
            $table->foreignId('scene_id')->nullable()->after('id')->constrained('vn_scenes')->cascadeOnDelete();
        });

        Schema::table('vn_dialogues', function (Blueprint $table) {
            $table->foreignId('scene_id')->nullable()->after('id')->constrained('vn_scenes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vn_characters', function (Blueprint $table) {
            $table->dropForeign(['scene_id']);
            $table->dropColumn('scene_id');
        });

        Schema::table('vn_backgrounds', function (Blueprint $table) {
            $table->dropForeign(['scene_id']);
            $table->dropColumn('scene_id');
        });

        Schema::table('vn_dialogues', function (Blueprint $table) {
            $table->dropForeign(['scene_id']);
            $table->dropColumn('scene_id');
        });
    }
};
