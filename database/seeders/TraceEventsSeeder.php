<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\TraceEvent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TraceEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a test user if none exists
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Get all batches
        $batches = Batch::all();
        
        if ($batches->isEmpty()) {
            $this->command->info('No batches found. Please run the TestDataSeeder first.');
            return;
        }

        $eventTypes = [
            'harvest', 'processing', 'quality_check', 'packaging', 'shipping', 'delivery',
            'storage', 'temperature_check', 'inspection', 'certification'
        ];

        $locations = [
            'Main Warehouse', 'Processing Plant A', 'Cold Storage 1', 'Shipping Dock', 
            'Quality Lab', 'Packaging Line 1', 'Farm A', 'Distribution Center'
        ];

        foreach ($batches as $batch) {
            $previousHash = null;
            $eventCount = rand(3, 8); // 3-8 events per batch
            
            for ($i = 0; $i < $eventCount; $i++) {
                $eventType = $eventTypes[array_rand($eventTypes)];
                $location = $locations[array_rand($locations)];
                $timestamp = now()->subDays(rand(1, 30));
                
                // Generate a unique hash for this event
                $currentHash = hash('sha256', 
                    $batch->id . 
                    $eventType . 
                    $timestamp . 
                    $previousHash . 
                    Str::random(10)
                );
                
                $event = TraceEvent::create([
                    'batch_id' => $batch->id,
                    'event_type' => $eventType,
                    'actor_id' => $user->id,
                    'location' => $location,
                    'reference_document' => 'DOC-' . strtoupper(Str::random(8)),
                    'data' => json_encode([
                        'notes' => 'Sample event data for ' . $eventType,
                        'temperature' => rand(2, 25) . '°C',
                        'humidity' => rand(30, 80) . '%',
                        'inspector' => $user->name,
                    ]),
                    'previous_event_hash' => $previousHash,
                    'current_hash' => $currentHash,
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'is_corrective_action' => rand(0, 1) === 1,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
                
                $previousHash = $currentHash;
                
                // Update batch status based on the latest event
                $batch->update(['status' => $eventType]);
            }
            
            // Add a final 'completed' event for each batch
            $currentHash = hash('sha256', $batch->id . 'completed' . now() . $previousHash . Str::random(10));
            
            TraceEvent::create([
                'batch_id' => $batch->id,
                'event_type' => 'completed',
                'actor_id' => $user->id,
                'location' => 'Final Destination',
                'reference_document' => 'COMPLETION-' . strtoupper(Str::random(6)),
                'data' => json_encode([
                    'notes' => 'Batch processing completed',
                    'final_status' => 'completed',
                    'completed_by' => $user->name,
                ]),
                'previous_event_hash' => $previousHash,
                'current_hash' => $currentHash,
                'ip_address' => '192.168.1.' . rand(1, 255),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Update batch status to completed
            $batch->update(['status' => 'completed']);
        }
        
        $this->command->info('Successfully seeded trace events for ' . $batches->count() . ' batches.');
    }
}
