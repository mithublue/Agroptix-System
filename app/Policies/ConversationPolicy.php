<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }
        return $conversation->customer_id === $user->id || $conversation->supplier_id === $user->id;
    }

    public function create(User $user): bool
    {
        // Any authenticated user can start a conversation (controller will validate participants)
        return (bool) $user->id;
    }

    public function update(User $user, Conversation $conversation): bool
    {
        // Allow closing/updating by admin or supplier (adjust as needed)
        return $this->isAdmin($user) || $conversation->supplier_id === $user->id;
    }

    protected function isAdmin(User $user): bool
    {
        return $user->getRoleNames()->map(fn($r) => strtolower($r))->contains('admin');
    }
}
