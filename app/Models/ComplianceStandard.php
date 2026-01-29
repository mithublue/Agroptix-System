<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceStandard extends Model
{
    protected $fillable = [
        'region',
        'crop_type',
        'parameter_name',
        'min_value',
        'max_value',
        'unit',
        'critical_action',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_value' => 'float',
        'max_value' => 'float',
    ];
}
