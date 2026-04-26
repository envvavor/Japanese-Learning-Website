<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(QuizItem::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Check if a specific user has passed this quiz with 100%.
     */
    public function isPassedByUser(int $userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }

    /**
     * Get the best attempt score for a user.
     */
    public function bestScoreForUser(int $userId): float
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->max('score') ?? 0;
    }
}
