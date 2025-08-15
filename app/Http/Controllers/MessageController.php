<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $data = $request->validate([
            'body' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'send_as_supplier' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        // Only admins can send as supplier
        $sendAsSupplier = $this->isAdmin($user) && $request->boolean('send_as_supplier');

        // Ensure at least body or one attachment
        if ((!isset($data['body']) || trim((string)$data['body']) === '') && empty($request->file('attachments', []))) {
            return back()->withErrors(['body' => 'Please enter a message or attach a file.'])->withInput();
        }

        return DB::transaction(function () use ($conversation, $user, $data, $sendAsSupplier, $request) {
            $attachmentsMeta = [];
            $files = $request->file('attachments', []);
            foreach ($files as $file) {
                if (!$file) continue;
                $path = $file->store("messages/{$conversation->id}", 'public');
                $attachmentsMeta[] = [
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'author_id' => $user->id,
                'sent_as_user_id' => $sendAsSupplier ? $conversation->supplier_id : null,
                'type' => 'text',
                'body' => $data['body'] ?? null,
                'attachments' => $attachmentsMeta ?: null,
            ]);

            $conversation->forceFill(['last_message_at' => now()])->save();

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'data' => $message], 201);
            }

            return redirect()->route('conversations.show', $conversation)->with('success', 'Message sent');
        });
    }

    private function isAdmin($user): bool
    {
        return $user->getRoleNames()->map(fn ($r) => strtolower($r))->contains('admin');
    }
}
