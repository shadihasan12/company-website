<?php

namespace App\Models;

use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    /**
     * Leads are created from public form input, so the fillable list is an
     * allow-list rather than `$guarded = []`. Nothing the visitor sends may
     * set `status`, `contacted_at` or `notes`.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'service_id',
        'budget_range',
        'timeline',
        'message',
        'source',
        'payload',
        'locale',
        'referrer',
        'ip_address',
        'user_agent',
    ];

    /**
     * Mirrors the database defaults so a freshly created model reports the
     * same state as one read back from the database.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'source' => 'contact',
        'status' => 'new',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'contacted_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** @param  Builder<$this>  $query */
    public function scopeNew(Builder $query): void
    {
        $query->where('status', 'new');
    }

    /** @param  Builder<$this>  $query */
    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderByDesc('created_at');
    }
}
