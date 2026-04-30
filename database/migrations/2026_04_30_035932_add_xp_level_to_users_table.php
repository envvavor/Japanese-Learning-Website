<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'xp')) {
                $table->integer('xp')->default(0)->after('has_seen_onboarding');
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->integer('level')->default(1)->after('xp');
            }
            if (!Schema::hasColumn('users', 'next_level_xp')) {
                $table->integer('next_level_xp')->default(100)->after('level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['xp', 'level', 'next_level_xp'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
