<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TraceEvent extends Model
{
    use HasFactory;

    // Event types as constants
    public const TYPE_HARVEST = 'harvested';
    public const TYPE_PROCESSING = 'processing';
    public const TYPE_QC_PENDING = 'qc_pending';
    public const TYPE_QC_APPROVED = 'qc_approved';
    public const TYPE_QC_REJECTED = 'qc_rejected';
    public const TYPE_PACKAGING = 'packaging';
    public const TYPE_PACKAGED = 'packaged';
    public const TYPE_SHIPPED = 'shipped';
    public const TYPE_DELIVERED = 'delivered';
    public const TYPE_QUARANTINE = 'quarantine';
    public const TYPE_DISPOSED = 'disposed';
    public const TYPE_PROCESSING_DELETED = 'processing_deleted';
    public const TYPE_SHIPPING_DELETED = 'shipping_deleted';
    public const TYPE_PACKAGING_DELETED = 'packaging_deleted';
    public const TYPE_QC_REJECTION = 'qc_rejection';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'batch_id',
        'event_type',
        'actor_id',
        'location',
        'reference_document',
        'data',
        'previous_event_hash',
        'current_hash',
        'digital_signature',
        'device_id',
        'ip_address',
        'is_corrective_action',
        'parent_event_id',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'custom_fields' => 'array',
        'is_corrective_action' => 'boolean',
    ];

    /**
     * Get the event types as an array for dropdowns
     */
    public static function getEventTypes(): array
    {
        return [
            self::TYPE_HARVEST => 'Harvested',
            self::TYPE_PROCESSING => 'Processing',
            self::TYPE_QC_PENDING => 'QC Pending',
            self::TYPE_QC_APPROVED => 'QC Approved',
            self::TYPE_QC_REJECTED => 'QC Rejected',
            self::TYPE_PACKAGING => 'Packaging',
            self::TYPE_PACKAGED => 'Packaged',
            self::TYPE_SHIPPED => 'Shipped',
            self::TYPE_DELIVERED => 'Delivered',
            self::TYPE_QUARANTINE => 'Quarantined',
            self::TYPE_DISPOSED => 'Disposed',
        ];
    }

    /**
     * Get the batch that owns the trace event.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the actor (user) who performed the event.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Get the parent event if this is a follow-up event.
     */
    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(TraceEvent::class, 'parent_event_id');
    }

    /**
     * Get any child events that reference this event as parent.
     */
    public function childEvents(): HasMany
    {
        return $this->hasMany(TraceEvent::class, 'parent_event_id');
    }

    /**
     * Get the next event in the sequence.
     */
    public function nextEvent(): HasOne
    {
        return $this->hasOne(TraceEvent::class, 'previous_event_id');
    }

    /**
     * Generate a hash for the event data.
     */
    public function generateHash(): string
    {
        $data = [
            'batch_id' => $this->batch_id,
            'event_type' => $this->event_type,
            'timestamp' => $this->created_at ? $this->created_at->toIso8601String() : now()->toIso8601String(),
            'actor_id' => $this->actor_id,
            'location' => $this->location,
            'data' => $this->data,
            'previous_hash' => $this->previous_event_hash,
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * The "booting" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($traceEvent) {
            // Set the current hash when creating a new event
            $traceEvent->current_hash = $traceEvent->generateHash();
        });
    }

    /**
     * Get the display name for the event type.
     */
    public function getEventTypeDisplayAttribute(): string
    {
        return self::getEventTypes()[$this->event_type] ?? Str::title(str_replace('_', ' ', $this->event_type));
    }

    /**
     * Scope a query to only include events of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Check if the event is of a specific type.
     */
    public function isOfType(string $type): bool
    {
        return $this->event_type === $type;
    }

    /**
     * Verify the integrity of the event chain.
     */
    public function verifyIntegrity(): bool
    {
        if ($this->current_hash !== $this->generateHash()) {
            return false;
        }

        if ($this->previous_event_id) {
            $previous = self::find($this->previous_event_id);
            if (!$previous || $previous->current_hash !== $this->previous_event_hash) {
                return false;
            }
        }

        return true;
    }
}
