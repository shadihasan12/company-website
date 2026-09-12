<?php

namespace App\Models;

use App\Casts\Translated;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'summary' => Translated::class,
            'problem' => Translated::class,
            'solution' => Translated::class,
            'outcome' => Translated::class,
            'duration' => Translated::class,
            'metrics' => 'array',
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'completed_at' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** @return BelongsTo<Industry, $this> */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /** @return BelongsToMany<Service, $this> */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /** @return BelongsToMany<Technology, $this> */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }

    /** @return HasMany<Testimonial, $this> */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Whether this case study has the evidence that actually persuades:
     * screenshots, numbers, and somewhere to go and see it for real.
     *
     * Used by the admin panel and the seeder report to show at a glance
     * which projects are still missing proof.
     */
    protected function isEvidenced(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->gallery)
            && filled($this->metrics)
            && $this->hasPublicLink);
    }

    protected function hasPublicLink(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->website_url)
            || filled($this->app_store_url)
            || filled($this->google_play_url));
    }

    /** @param  Builder<$this>  $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /** @param  Builder<$this>  $query */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /** @param  Builder<$this>  $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('completed_at')->orderByDesc('id');
    }
}
