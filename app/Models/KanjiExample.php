<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanjiExample extends Model
{
    use HasFactory;

    protected $fillable = [
        'kanji_id',
        'japanese_text',
        'furigana_html',
        'meaning'
    ];

    // Relasi balik ke Kanji
    public function kanji()
    {
        return $this->belongsTo(Kanji::class);
    }
}