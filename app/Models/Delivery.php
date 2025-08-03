<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'delivery_date',
        'delivery_notes',
        'delivery_person',
        'delivery_contact',
        'delivery_address',
        'delivery_status',
        'signature_recipient_name',
        'signature_data',
        'delivery_confirmation',
        'temperature_check',
        'quality_check',
        'additional_notes',
        'delivery_photos',
        'customer_rating',
        'customer_comments',
        'customer_complaints',
        'feedback_photos',
        'feedback_submitted_at',
        'feedback_status',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'delivery_date' => 'datetime',
        'delivery_confirmation' => 'boolean',
        'temperature_check' => 'boolean',
        'quality_check' => 'boolean',
        'delivery_photos' => 'array',
        'feedback_photos' => 'array',
        'feedback_submitted_at' => 'datetime',
    ];

    /**
     * Get the batch that owns the delivery.
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Scope a query to only include pending deliveries.
     */
    public function scopePending($query)
    {
        return $query->where('delivery_status', 'pending');
    }

    /**
     * Scope a query to only include completed deliveries.
     */
    public function scopeCompleted($query)
    {
        return $query->where('delivery_status', 'delivered');
    }

    /**
     * Check if delivery is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->delivery_confirmation === true;
    }

    /**
     * Check if all quality checks are passed.
     */
    public function hasPassedQualityChecks(): bool
    {
        return $this->temperature_check === true && $this->quality_check === true;
    }
}
