<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'batch_code',
        'source_id',
        'product_id',
        'harvest_time',
        'status',
        'source_as_source_id',
        'product_as_product_id',
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
            'source_id' => 'integer',
            'product_id' => 'integer',
            'source_as_source_id' => 'integer',
            'product_as_product_id' => 'integer',
        ];
    }

    public function sourceAsSource(): BelongsTo
    {
        return $this->belongsTo(SourceAsSource::class);
    }

    public function productAsProduct(): BelongsTo
    {
        return $this->belongsTo(ProductAsProduct::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Sources,::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products,::class);
    }
}
