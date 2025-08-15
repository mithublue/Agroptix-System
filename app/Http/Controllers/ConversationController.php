<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Conversation::with(['customer:id,name', 'supplier:id,name'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at');

        // Non-admins: only their own conversations
        if (!$this->isAdmin($user)) {
            $query->where(function ($q) use ($user) {
                $q->where('customer_id', $user->id)
                  ->orWhere('supplier_id', $user->id);
            });
        }

        // Optional filter by participant id
        if ($request->filled('participant_id')) {
            $pid = (int) $request->input('participant_id');
            $query->where(function ($q) use ($pid) {
                $q->where('customer_id', $pid)->orWhere('supplier_id', $pid);
            });
        }

        $conversations = $query->paginate(20);

        if ($request->wantsJson()) {
            return response()->json(['data' => $conversations]);
        }

        return view('messaging.index', compact('conversations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:users,id'],
            'supplier_id' => ['required', 'exists:users,id', 'different:customer_id'],
            'subject_type' => ['nullable', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer'],
            'body' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        // Only admin or one of the participants can open a conversation
        if (!$this->isAdmin($user) && !in_array($user->id, [(int)$data['customer_id'], (int)$data['supplier_id']], true)) {
            abort(403, 'Unauthorized');
        }

        return DB::transaction(function () use ($data, $user, $request) {
            $conversation = Conversation::create([
                'customer_id' => $data['customer_id'],
                'supplier_id' => $data['supplier_id'],
                'subject_type' => $data['subject_type'] ?? null,
                'subject_id' => $data['subject_id'] ?? null,
                'created_by' => $user->id,
                'last_message_at' => now(),
            ]);

            // Ensure participants rows
            $participants = [
                ['conversation_id' => $conversation->id, 'user_id' => (int)$data['customer_id'], 'last_read_at' => null],
                ['conversation_id' => $conversation->id, 'user_id' => (int)$data['supplier_id'], 'last_read_at' => null],
            ];
            foreach ($participants as $p) {
                ConversationParticipant::firstOrCreate([
                    'conversation_id' => $p['conversation_id'],
                    'user_id' => $p['user_id'],
                ], [
                    'last_read_at' => $p['last_read_at'],
                ]);
            }

            // Optional first message
            if (!empty($data['body'])) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'author_id' => $user->id,
                    'body' => $data['body'],
                    'type' => 'text',
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'data' => $conversation], 201);
            }

            return redirect()->route('conversations.show', $conversation)->with('success', 'Conversation started');
        });
    }

    public function show(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['customer:id,name', 'supplier:id,name']);

        $messages = Message::with(['author:id,name', 'sentAs:id,name'])
            ->where('conversation_id', $conversation->id)
            ->orderBy('created_at')
            ->paginate(30);

        // Mark as read for the current user
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $request->user()->id)
            ->update(['last_read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => [
                    'conversation' => $conversation,
                    'messages' => $messages,
                ],
            ]);
        }

        return view('messaging.show', compact('conversation', 'messages'));
    }

    private function isAdmin(User $user): bool
    {
        // Case-insensitive check for 'admin' role
        $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));
        return $roles->contains('admin');
    }
}
