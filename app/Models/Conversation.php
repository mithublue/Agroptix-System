<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id','supplier_id','subject_type','subject_id','created_by','last_message_at','is_closed','closed_by_id','closed_at'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function supplier(): BelongsTo { return $this->belongsTo(User::class, 'supplier_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function closer(): BelongsTo { return $this->belongsTo(User::class, 'closed_by_id'); }

    public function messages(): HasMany { return $this->hasMany(Message::class); }
    public function participants(): HasMany { return $this->hasMany(ConversationParticipant::class); }

    public function subject()
    {
        return $this->morphTo();
    }
}
