<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    protected $fillable = [
        'original',
        'furigana',
        'english',
        'jlpt_level',
    ];

    /**
     * Scope: filter by JLPT level
     */
    public function scopeByLevel($query, string $level)
    {
        return $query->where('jlpt_level', strtoupper($level));
    }

    /**
     * Scope: full-text search across original, furigana, english
     */
    public function scopeSearch($query, string $q)
    {
        return $query->where(function ($sub) use ($q) {
            $sub->where('original',  'like', "%{$q}%")
                ->orWhere('furigana', 'like', "%{$q}%")
                ->orWhere('english',  'like', "%{$q}%");
        });
    }

    /**
     * Return the JLPT level color class (Tailwind)
     */
    public function getLevelColorAttribute(): string
    {
        return match ($this->jlpt_level) {
            'N1' => 'rose',
            'N2' => 'orange',
            'N3' => 'amber',
            'N4' => 'emerald',
            'N5' => 'sky',
            default => 'gray',
        };
    }
}