<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VnScene extends Model
{
    protected $table = 'vn_scenes';

    protected $fillable = [
        'title',
        'description',
        'thumbnail_path',
        'first_dialogue_id',
    ];

    public function firstDialogue(): BelongsTo
    {
        return $this->belongsTo(VnDialogue::class, 'first_dialogue_id');
    }

    public function characters(): HasMany
    {
        return $this->hasMany(VnCharacter::class, 'scene_id');
    }

    public function backgrounds(): HasMany
    {
        return $this->hasMany(VnBackground::class, 'scene_id');
    }

    public function dialogues(): HasMany
    {
        return $this->hasMany(VnDialogue::class, 'scene_id');
    }
}
