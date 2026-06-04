<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyFolderItem extends Model
{
    protected $fillable = [
        'folder_id',
        'vocabulary_id',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(VocabularyFolder::class, 'folder_id');
    }

    public function vocabulary(): BelongsTo
    {
        return $this->belongsTo(Vocabulary::class);
    }
}
