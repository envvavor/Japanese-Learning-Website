<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kanji extends Model
{
    use HasFactory;

    protected $fillable = ['character', 'meaning', 'strokes', 'category', 'level', 'stroke_order_image', 'kunyomi', 'onyomi'];

    protected $casts = [
        'strokes' => 'array',
    ];

    public function examples()
    {
        return $this->hasMany(KanjiExample::class);
    }
}