<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Source extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'gps_lat',
        'gps_long',
        'production_method',
        'area',
        'status',
        'user_as_owner_id',
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
            'user_as_owner_id' => 'integer',
        ];
    }

    public function userAsOwner(): BelongsTo
    {
        return $this->belongsTo(UserAsOwner::class);
    }
}
