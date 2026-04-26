<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'quiz_type',
        'total_questions',
        'correct_answers',
        'score',
        'questions_with_text_revealed',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'session_id');
    }

    public function getPassedAttribute(): bool
    {
        return $this->score >= 70;
    }

    public function getGradeAttribute(): string
    {
        if ($this->score >= 90) return 'S';
        if ($this->score >= 80) return 'A';
        if ($this->score >= 70) return 'B';
        if ($this->score >= 60) return 'C';
        if ($this->score >= 50) return 'D';
        return 'F';
    }
}
