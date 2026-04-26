<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'google_avatar')) {
                $table->string('google_avatar')->nullable()->after('google_id');
            }
            if (!Schema::hasColumn('users', 'password')) {
                // password may already exist, skip
            } else {
                // Make password nullable so Google users don't need one
                $table->string('password')->nullable()->change();
            }
            if (!Schema::hasColumn('users', 'verification_code')) {
                $table->string('verification_code', 6)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'verification_code_expires_at')) {
                $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('google_id');
            $table->dropColumnIfExists('google_avatar');
            $table->dropColumnIfExists('verification_code');
            $table->dropColumnIfExists('verification_code_expires_at');
        });
    }
};
