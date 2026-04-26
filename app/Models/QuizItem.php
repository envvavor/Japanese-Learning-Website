<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_type',
        'question_text',
        'correct_answer',
        'options',
        'audio_url',
        'kanji_id',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function kanji()
    {
        return $this->belongsTo(Kanji::class);
    }
}
