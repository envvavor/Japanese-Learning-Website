<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure table exists first (the original migration was empty)
        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            return; // All columns already created above
        }

        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'title')) {
                $table->string('title')->after('id');
            }
            if (!Schema::hasColumn('quizzes', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('quizzes', 'order')) {
                $table->integer('order')->default(0)->after('description');
            }
            if (!Schema::hasColumn('quizzes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
