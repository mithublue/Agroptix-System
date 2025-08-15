<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id', 'author_id', 'sent_as_user_id', 'type', 'body', 'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function conversation(): BelongsTo { return $this->belongsTo(Conversation::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function sentAs(): BelongsTo { return $this->belongsTo(User::class, 'sent_as_user_id'); }
}
