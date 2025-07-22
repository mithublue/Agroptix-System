<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    use HasFactory;

    protected $table = 'packaging';

    protected $fillable = [
        'batch_id',
        'qr_code',
        'package_type',
        'material_type',
        'unit_weight_packaging',
        'total_product_weight',
        'total_package_weight',
        'quantity_of_units',
        'packaging_start_time',
        'packaging_end_time',
        'packaging_location',
        'packer_id',
        'rpc_unit_id',
        'cleanliness_checklist',
        'co2_estimate',
    ];

    protected $casts = [
        'packaging_start_time' => 'datetime',
        'packaging_end_time' => 'datetime',
        'unit_weight_packaging' => 'decimal:3',
        'total_product_weight' => 'decimal:3',
        'total_package_weight' => 'decimal:3',
        'quantity_of_units' => 'integer',
        'cleanliness_checklist' => 'boolean',
        'co2_estimate' => 'decimal:3',
    ];

    /**
     * Get the batch that owns the packaging.
     */
    public function batch()
    {
        return $this->belongsTo(\App\Models\Batch::class);
    }

    /**
     * Get the user who packed this.
     */
    public function packer()
    {
        return $this->belongsTo(\App\Models\User::class, 'packer_id');
    }

    /**
     * Get the RPC unit used for this packaging, if any.
     */
    public function rpcUnit()
    {
        return $this->belongsTo(\App\Models\RpcUnit::class, 'rpc_unit_id');
    }
}
