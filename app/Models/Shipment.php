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
        'fuel_type',
        'temperature',
        'mode',
        'route_distance',
        'current_location_lat',
        'current_location_lng',
        'last_location_update',
        'tracking_status',
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
            'current_location_lat' => 'decimal:8',
            'current_location_lng' => 'decimal:8',
            'last_location_update' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
