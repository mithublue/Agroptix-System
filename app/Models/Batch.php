<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class Batch extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_CREATED = 'created';
    public const STATUS_HARVESTED = 'harvested';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_QC_PENDING = 'qc_pending';
    public const STATUS_QC_APPROVED = 'qc_approved';
    public const STATUS_QC_REJECTED = 'qc_rejected';
    public const STATUS_PACKAGING = 'packaging';
    public const STATUS_PACKAGED = 'packaged';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_QUARANTINED = 'quarantined';
    public const STATUS_DISPOSED = 'disposed';

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
        'trace_code',
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
        'has_defect' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::STATUS_CREATED,
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($batch) {
            if (empty($batch->trace_code)) {
                $batch->trace_code = static::generateTraceCode();
            }
        });
    }

    /**
     * Generate a unique trace code for the batch.
     */
    public static function generateTraceCode(): string
    {
        do {
            $code = 'B' . strtoupper(Str::random(3)) . now()->format('ymd') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        } while (static::where('trace_code', $code)->exists());

        return $code;
    }

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
    /**
     * Get the source associated with the batch.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the trace events for the batch.
     */
    public function traceEvents(): HasMany
    {
        return $this->hasMany(TraceEvent::class);
    }

    /**
     * Get the latest trace event for the batch.
     */
    public function latestTraceEvent(): HasOne
    {
        return $this->hasOne(TraceEvent::class)->latestOfMany();
    }

    /**
     * Get the display name of the batch.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->batch_code ?: "Batch #{$this->id}";
    }

    /**
     * Get the URL to view the batch's traceability timeline.
     */
    public function getTraceabilityUrl(): string
    {
        return route('batches.trace', $this->trace_code);
    }

    /**
     * Check if the batch can transition to the given status.
     */
    public function canTransitionTo(string $status): bool
    {
        $transitions = [
            self::STATUS_CREATED => [self::STATUS_HARVESTED],
            self::STATUS_HARVESTED => [self::STATUS_PROCESSING],
            self::STATUS_PROCESSING => [self::STATUS_QC_PENDING],
            self::STATUS_QC_PENDING => [self::STATUS_QC_APPROVED, self::STATUS_QC_REJECTED],
            self::STATUS_QC_APPROVED => [self::STATUS_PACKAGING],
            self::STATUS_QC_REJECTED => [self::STATUS_QUARANTINED, self::STATUS_DISPOSED],
            self::STATUS_PACKAGING => [self::STATUS_PACKAGED],
            self::STATUS_PACKAGED => [self::STATUS_SHIPPED],
            self::STATUS_SHIPPED => [self::STATUS_DELIVERED],
            self::STATUS_QUARANTINED => [self::STATUS_PROCESSING, self::STATUS_DISPOSED],
            // No transitions from DISPOSED or DELIVERED
        ];

        return in_array($status, $transitions[$this->status] ?? []);
    }

    /**
     * Get the QR code for this batch.
     */
    public function getQrCode()
    {
        return QrCode::size(200)
            ->format('svg')
            ->generate($this->getTraceabilityUrl());
    }

    /**
     * Get the product that owns the batch.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the quality tests for the batch.
     */
    public function qualityTests(): HasMany
    {
        return $this->hasMany(QualityTest::class);
    }

    /**
     * Get all eco processes for the batch.
     */
    public function ecoProcesses(): HasMany
    {
        return $this->hasMany(EcoProcess::class);
    }

    /**
     * Get the packaging records for the batch.
     */
    public function packaging()
    {
        return $this->hasOne(Packaging::class);
    }

    /**
     * Get the delivery associated with the batch.
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
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
}
