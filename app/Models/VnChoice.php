<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VnChoice extends Model
{
    protected $table = 'vn_choices';

    protected $fillable = [
        'dialogue_id',
        'choice_text',
        'target_dialogue_id',
    ];

    public function dialogue(): BelongsTo
    {
        return $this->belongsTo(VnDialogue::class, 'dialogue_id');
    }

    public function targetDialogue(): BelongsTo
    {
        return $this->belongsTo(VnDialogue::class, 'target_dialogue_id');
    }
}
