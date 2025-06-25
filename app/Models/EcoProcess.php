<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcoProcess extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'stage',
        'data',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Get the batch that owns the eco process.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
