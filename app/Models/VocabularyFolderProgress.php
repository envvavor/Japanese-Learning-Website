<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyFolderProgress extends Model
{
    protected $table = 'vocabulary_folder_progress';

    protected $fillable = [
        'user_id',
        'folder_id',
        'vocabulary_id',
        'is_correct',
        'attempts',
        'last_practiced_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'last_practiced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(VocabularyFolder::class, 'folder_id');
    }

    public function vocabulary(): BelongsTo
    {
        return $this->belongsTo(Vocabulary::class);
    }
}
