<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityTest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'batch_id',
        'user_id',
        'parameter_tested',
        'result',
        'result_status',
        'batch_as_batch_id',
        'user_as_user_id',
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
            'user_id' => 'integer',
            'batch_as_batch_id' => 'integer',
            'user_as_user_id' => 'integer',
        ];
    }

    public function batchAsBatch(): BelongsTo
    {
        return $this->belongsTo(BatchAsBatch::class);
    }

    public function userAsUser(): BelongsTo
    {
        return $this->belongsTo(UserAsUser::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batches,::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users,::class);
    }
}
