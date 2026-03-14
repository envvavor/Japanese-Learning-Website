<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VnDialogue extends Model
{
    protected $table = 'vn_dialogues';

    protected $fillable = [
        'scene_id',
        'character_id',
        'background_id',
        'original_text',
        'translated_text',
        'audio_file_path',
        'next_dialogue_id',
        'position_x',
        'position_y',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(VnCharacter::class, 'character_id');
    }

    public function background(): BelongsTo
    {
        return $this->belongsTo(VnBackground::class, 'background_id');
    }

    public function choices(): HasMany
    {
        return $this->hasMany(VnChoice::class, 'dialogue_id');
    }

    public function nextDialogue(): BelongsTo
    {
        return $this->belongsTo(VnDialogue::class, 'next_dialogue_id');
    }
}
