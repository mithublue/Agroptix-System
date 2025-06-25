<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Batch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_code',
        'source_id',
        'product_id',
        'harvest_time',
        'status',
        'weight',
        'grade',
        'has_defect',
        'remark',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harvest_time' => 'datetime',
        'source_id' => 'integer',
        'product_id' => 'integer',
    ];

    /**
     * The possible status values for a batch.
     *
     * @var array<string, string>
     */
    /**
     * The possible grade values for a batch.
     *
     * @var array<string, string>
     */
    public const GRADES = [
        'A' => 'A (Premium)',
        'B' => 'B (Good)',
        'C' => 'C (Average)',
        'D' => 'D (Below Average)',
    ];

    /**
     * The possible status values for a batch.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Get the source associated with the batch.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the product associated with the batch.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get all eco processes for the batch.
     */
    public function ecoProcesses(): HasMany
    {
        return $this->hasMany(EcoProcess::class);
    }

    /**
     * Scope a query to only include batches of a given status.
     */
    public function scopeStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope a query to only include batches harvested after a given date.
     */
    public function scopeHarvestedAfter(Builder $query, string $date): void
    {
        $query->where('harvest_time', '>=', Carbon::parse($date));
    }

    /**
     * Get the display name of the batch.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->batch_code ?: "Batch #{$this->id}";
    }
}
