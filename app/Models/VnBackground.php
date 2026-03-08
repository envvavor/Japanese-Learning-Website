<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VnBackground extends Model
{
    protected $table = 'vn_backgrounds';

    protected $fillable = [
        'scene_id',
        'name',
        'image_path',
    ];

    public function scene(): BelongsTo
    {
        return $this->belongsTo(VnScene::class, 'scene_id');
    }

    public function dialogues(): HasMany
    {
        return $this->hasMany(VnDialogue::class, 'background_id');
    }
}
