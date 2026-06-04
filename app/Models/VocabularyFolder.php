<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocabularyFolder extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'user_id',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VocabularyFolderItem::class, 'folder_id');
    }

    public function vocabularies(): BelongsToMany
    {
        return $this->belongsToMany(Vocabulary::class, 'vocabulary_folder_items', 'folder_id', 'vocabulary_id')
                    ->withTimestamps();
    }

    public function progress(): HasMany
    {
        return $this->hasMany(VocabularyFolderProgress::class, 'folder_id');
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function isAdminFolder(): bool
    {
        return $this->user_id === null;
    }

    public function getWordCountAttribute(): int
    {
        return $this->items()->count();
    }

    public function progressForUser(int $userId): array
    {
        $total = $this->items()->count();
        if ($total === 0) return ['correct' => 0, 'total' => 0, 'percent' => 0];

        $correct = $this->progress()
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->count();

        return [
            'correct' => $correct,
            'total' => $total,
            'percent' => round(($correct / $total) * 100),
        ];
    }

    public static array $availableColors = [
        'indigo', 'rose', 'emerald', 'amber', 'sky', 'violet', 'orange', 'cyan', 'pink', 'teal',
    ];
}
