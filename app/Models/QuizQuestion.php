<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'kanji_id',
        'question_type',
        'question_subtype',
        'question_text',
        'correct_answer',
        'options',
        'user_answer',
        'is_correct',
        'accuracy_score',
        'time_taken_seconds',
        'audio_url',
        'text_was_revealed',
        'hint_was_used',
        'points_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'accuracy_score' => 'float',
        'options' => 'array',
        'text_was_revealed' => 'boolean',
        'hint_was_used' => 'boolean',
    ];

    public function quizSession()
    {
        return $this->belongsTo(QuizSession::class, 'session_id');
    }

    public function kanji()
    {
        return $this->belongsTo(Kanji::class);
    }
}
