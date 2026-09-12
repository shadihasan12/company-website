<?php

namespace App\Models;

use App\Casts\Translated;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'anonymous_label' => Translated::class,
            'is_named' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * What the public site is allowed to call this client.
     *
     * Naming a client without written permission is a real problem, so the
     * real name is only ever surfaced when `is_named` is true. Everything
     * public reads this accessor rather than `name` directly.
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => $this->is_named
            ? $this->name
            : ((string) $this->anonymous_label ?: __('A confidential client')));
    }

    /** @return HasMany<Project, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** @return HasMany<Testimonial, $this> */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Clients whose logo may appear in the logo strip: named, permitted,
     * and actually in possession of a logo file.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeShowcaseable(Builder $query): void
    {
        $query->where('is_named', true)->whereNotNull('logo_path');
    }

    /**
     * Clients we are permitted to name.
     *
     * Broader than {@see scopeShowcaseable()}: the logo strip falls back to
     * a text wordmark when no logo file has been supplied yet, so it is
     * useful before the artwork arrives.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeNamed(Builder $query): void
    {
        $query->where('is_named', true);
    }

    /** @param  Builder<$this>  $query */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /** @param  Builder<$this>  $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }
}
