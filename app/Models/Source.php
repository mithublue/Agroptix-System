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
        'address_line1',
        'address_line2',
        'country_code',
        'state',
        'status',
        'owner_id'
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
            'owner_id' => 'integer'
        ];
    }

    public function userAsOwner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the owner of the source.
     */
    public function owner(): BelongsTo
    {
        // This is the correct way to define the relationship
        return $this->belongsTo(User::class, 'owner_id');
    }
}
