<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'batch_id',
        'origin',
        'destination',
        'vehicle_type',
        'co2_estimate',
        'departure_time',
        'arrival_time',
        'batch_as_batch_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'batch_id' => 'integer',
            'co2_estimate' => 'decimal:2',
            'batch_as_batch_id' => 'integer',
        ];
    }

    public function batchAsBatch(): BelongsTo
    {
        return $this->belongsTo(BatchAsBatch::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batches,::class);
    }
}
