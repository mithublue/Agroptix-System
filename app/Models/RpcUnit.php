<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpcUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'rpc_identifier',
        'capacity_kg',
        'material_type',
        'initial_purchase_date',
        'last_washed_date',
        'total_wash_cycles',
        'total_reuse_count',
        'current_location',
        'status',
    ];

    protected $casts = [
        'initial_purchase_date' => 'date',
        'last_washed_date' => 'datetime',
        'capacity_kg' => 'decimal:2',
        'total_wash_cycles' => 'integer',
        'total_reuse_count' => 'integer',
    ];

    /**
     * Get the packaging instances that used this RPC unit.
     */
    public function packaging()
    {
        return $this->hasMany(\App\Models\Packaging::class, 'rpc_unit_id');
    }
}
