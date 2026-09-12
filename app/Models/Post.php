<?php

namespace App\Models;

use App\Casts\Translated;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => Translated::class,
            'excerpt' => Translated::class,
            'body' => Translated::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Live posts only. A null `published_at` is a draft and a future one is
     * scheduled, so both stay hidden.
     *
     * @param  Builder<$this>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** @param  Builder<$this>  $query */
    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderByDesc('published_at');
    }
}
