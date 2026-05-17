<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix: ubah collation kolom 'character' dari utf8mb4_unicode_ci ke utf8mb4_bin
     * agar hiragana (あ) dan katakana (ア) dianggap karakter BERBEDA oleh MySQL.
     * 
     * Dengan utf8mb4_unicode_ci, MySQL menganggap あ == ア sehingga unique index
     * menolak insert katakana jika hiragana padanannya sudah ada.
     */
    public function up(): void
    {
        // Drop the existing unique index first
        Schema::table('kanjis', function (Blueprint $table) {
            $table->dropUnique('kanjis_character_unique');
        });

        // Change the column collation to utf8mb4_bin (binary comparison)
        DB::statement("ALTER TABLE kanjis MODIFY `character` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL");

        // Re-add the unique index
        Schema::table('kanjis', function (Blueprint $table) {
            $table->unique('character');
        });
    }

    public function down(): void
    {
        Schema::table('kanjis', function (Blueprint $table) {
            $table->dropUnique('kanjis_character_unique');
        });

        DB::statement("ALTER TABLE kanjis MODIFY `character` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

        Schema::table('kanjis', function (Blueprint $table) {
            $table->unique('character');
        });
    }
};
