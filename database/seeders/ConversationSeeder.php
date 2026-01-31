<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Message::truncate();
        ConversationParticipant::truncate();
        Conversation::truncate();
        // Also truncate message_reads table if it exists as it depends on messages
        \Illuminate\Support\Facades\DB::table('message_reads')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Get users
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->warn('Not enough users found. Please run TestUsersSeeder first.');
            return;
        }

        // Get subjects (batches and products)
        $batches = Batch::all();
        $products = Product::all();

        if ($batches->isEmpty() && $products->isEmpty()) {
            $this->command->warn('No batches or products found. Please run TestDataSeeder first.');
            return;
        }

        $messageTemplates = [
            'inquiry' => [
                'Hi, I would like to inquire about this {subject}.',
                'Can you provide more details about this {subject}?',
                'I am interested in purchasing this {subject}. What are the terms?',
                'Could you share the availability status of this {subject}?',
            ],
            'response' => [
                'Thank you for your inquiry. The {subject} is currently available.',
                'We can provide detailed specifications. When would you like to discuss?',
                'The {subject} meets all quality standards. Would you like to proceed?',
                'I can arrange a sample shipment if you are interested.',
            ],
            'follow_up' => [
                'Just following up on my previous message.',
                'Have you had a chance to review my inquiry?',
                'Looking forward to your response.',
                'Please let me know if you need any additional information.',
            ],
            'confirmation' => [
                'Great! Please proceed with the order.',
                'That sounds perfect. Let\'s finalize the details.',
                'I confirm the order. Please send the invoice.',
                'Excellent. When can we expect delivery?',
            ],
            'closing' => [
                'Thank you for your assistance!',
                'Looking forward to working with you.',
                'Thanks for the quick response.',
                'Appreciate your help with this matter.',
            ],
        ];

        // Create 20 conversations
        for ($i = 0; $i < 20; $i++) {
            // Select random users as customer and supplier
            $customer = $users->random();
            $supplier = $users->where('id', '!=', $customer->id)->random();

            // Select random subject (batch or product)
            $subjectType = rand(0, 1) === 0 && $batches->isNotEmpty() ? 'batch' : 'product';
            $subject = $subjectType === 'batch' ? $batches->random() : $products->random();

            // Determine if conversation is closed (30% chance)
            $isClosed = rand(1, 100) <= 30;
            $closedAt = $isClosed ? now()->subDays(rand(1, 10)) : null;
            $closedById = $isClosed ? [$customer->id, $supplier->id][array_rand([$customer->id, $supplier->id])] : null;

            // Create conversation
            $conversation = Conversation::create([
                'customer_id' => $customer->id,
                'supplier_id' => $supplier->id,
                'subject_type' => 'App\\Models\\' . ucfirst($subjectType),
                'subject_id' => $subject->id,
                'created_by' => $customer->id,
                'last_message_at' => now(),
                'is_closed' => $isClosed,
                'closed_by_id' => $closedById,
                'closed_at' => $closedAt,
            ]);

            // Create participants
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $customer->id,
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $supplier->id,
            ]);

            // Create messages (3-10 messages per conversation)
            $messageCount = rand(3, 10);
            $baseTime = $conversation->created_at;

            for ($j = 0; $j < $messageCount; $j++) {
                // Alternate between customer and supplier
                $author = $j % 2 === 0 ? $customer : $supplier;

                // Select message template based on position
                if ($j === 0) {
                    $template = $messageTemplates['inquiry'][array_rand($messageTemplates['inquiry'])];
                } elseif ($j === $messageCount - 1) {
                    $template = $messageTemplates['closing'][array_rand($messageTemplates['closing'])];
                } elseif ($j % 2 === 0) {
                    $template = $messageTemplates['follow_up'][array_rand($messageTemplates['follow_up'])];
                } elseif ($j === 1) {
                    $template = $messageTemplates['response'][array_rand($messageTemplates['response'])];
                } else {
                    $template = $messageTemplates['confirmation'][array_rand($messageTemplates['confirmation'])];
                }

                // Replace {subject} placeholder
                $subjectName = $subjectType === 'batch' ? 'batch ' . $subject->batch_code : 'product ' . $subject->name;
                $body = str_replace('{subject}', $subjectName, $template);

                // Calculate message time (incrementing from base time)
                $messageTime = $baseTime->copy()->addHours($j * rand(1, 12))->addMinutes(rand(0, 59));

                Message::create([
                    'conversation_id' => $conversation->id,
                    'author_id' => $author->id,
                    'sent_as_user_id' => $author->id,
                    'type' => 'text',
                    'body' => $body,
                    'attachments' => null,
                    'created_at' => $messageTime,
                    'updated_at' => $messageTime,
                ]);

                // Update conversation's last_message_at
                $conversation->update(['last_message_at' => $messageTime]);
            }
        }

        $this->command->info('Conversations seeded successfully!');
        $this->command->info('Created ' . Conversation::count() . ' conversations.');
        $this->command->info('Created ' . Message::count() . ' messages.');
        $this->command->info('Created ' . ConversationParticipant::count() . ' participants.');
    }
}
