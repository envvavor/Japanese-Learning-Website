<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content'];

    /**
     * Boot method to auto-generate slug from title.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($materi) {
            $slug = Str::slug($materi->title);
            $originalSlug = $slug;
            $counter = 1;

            // Handle duplicate slugs by appending a counter
            while (static::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $materi->slug = $slug;
        });

        static::updating(function ($materi) {
            if ($materi->isDirty('title')) {
                $slug = Str::slug($materi->title);
                $originalSlug = $slug;
                $counter = 1;

                while (static::where('slug', $slug)->where('id', '!=', $materi->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $materi->slug = $slug;
            }
        });
    }

    /**
     * Get the route key name for Laravel route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
