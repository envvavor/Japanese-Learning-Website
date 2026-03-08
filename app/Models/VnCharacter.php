<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VnCharacter extends Model
{
    protected $table = 'vn_characters';

    protected $fillable = [
        'scene_id',
        'name',
        'default_sprite_path',
        'elevenlabs_voice_id',
    ];

    public function scene(): BelongsTo
    {
        return $this->belongsTo(VnScene::class, 'scene_id');
    }

    public function dialogues(): HasMany
    {
        return $this->hasMany(VnDialogue::class, 'character_id');
    }
}
