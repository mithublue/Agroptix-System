<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing deliveries
        Delivery::truncate();

        // Get all batches
        $batches = Batch::all();

        if ($batches->isEmpty()) {
            $this->command->warn('No batches found. Please run TestDataSeeder first.');
            return;
        }

        $deliveryPersons = [
            'Mohammed Rahman',
            'Fatima Khan',
            'Abdul Karim',
            'Ayesha Begum',
            'Kamal Hossain',
            'Nadia Ahmed',
            'Rashid Ali',
            'Sultana Parvin',
        ];

        $addresses = [
            '123 Main Street, Dhaka 1000, Bangladesh',
            '456 Park Avenue, Chittagong 4000, Bangladesh',
            '789 Lake Road, Sylhet 3100, Bangladesh',
            '321 Market Street, Rajshahi 6000, Bangladesh',
            '654 Station Road, Khulna 9000, Bangladesh',
            '987 College Road, Rangpur 5400, Bangladesh',
            '147 Hospital Road, Mymensingh 2200, Bangladesh',
            '258 University Avenue, Comilla 3500, Bangladesh',
        ];

        $statuses = ['pending', 'in_transit', 'delivered', 'failed'];
        $statusWeights = [15, 25, 55, 5]; // 55% delivered, 25% in_transit, 15% pending, 5% failed

        // Create deliveries for 60% of batches
        foreach ($batches->random(min((int)($batches->count() * 0.6), $batches->count())) as $batch) {
            $deliveryPerson = $deliveryPersons[array_rand($deliveryPersons)];
            $address = $addresses[array_rand($addresses)];
            
            // Select status based on weights
            $statusIndex = $this->weightedRandom($statusWeights);
            $status = $statuses[$statusIndex];
            
            // Generate delivery date (within last 30 days or future for pending)
            if ($status === 'pending') {
                $deliveryDate = now()->addDays(rand(1, 14));
            } else {
                $deliveryDate = now()->subDays(rand(1, 30));
            }
            
            // Generate contact number
            $contact = '+880' . rand(1000000000, 1999999999);
            
            // Delivery confirmation and checks (only for delivered status)
            $deliveryConfirmation = $status === 'delivered';
            $temperatureCheck = $status === 'delivered' ? (rand(1, 100) > 10) : false; // 90% pass
            $qualityCheck = $status === 'delivered' ? (rand(1, 100) > 15) : false; // 85% pass
            
            // Customer feedback (only for delivered)
            $customerRating = null;
            $customerComments = null;
            $feedbackSubmittedAt = null;
            $feedbackStatus = null;
            
            if ($status === 'delivered' && rand(1, 100) <= 70) { // 70% of delivered orders have feedback
                $customerRating = rand(3, 5); // 3-5 stars
                $feedbackSubmittedAt = $deliveryDate->copy()->addHours(rand(1, 48));
                $feedbackStatus = 'submitted';
                
                $comments = [
                    'Excellent quality! Very fresh products.',
                    'Good delivery service. Products arrived in perfect condition.',
                    'Satisfied with the quality and timely delivery.',
                    'Products were fresh but packaging could be better.',
                    'Great service! Will order again.',
                    'Delivery was on time. Quality is good.',
                ];
                $customerComments = $comments[array_rand($comments)];
            }
            
            // Signature data (for delivered)
            $signatureRecipientName = null;
            $signatureData = null;
            if ($status === 'delivered') {
                $signatureRecipientName = 'Recipient ' . rand(1, 100);
                $signatureData = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            }
            
            // Additional notes
            $additionalNotes = null;
            if ($status === 'failed') {
                $failureReasons = [
                    'Customer not available at delivery address',
                    'Incorrect address provided',
                    'Customer refused delivery',
                    'Weather conditions prevented delivery',
                ];
                $additionalNotes = $failureReasons[array_rand($failureReasons)];
            }

            Delivery::create([
                'batch_id' => $batch->id,
                'delivery_date' => $deliveryDate,
                'delivery_notes' => 'Standard delivery procedure followed.',
                'delivery_person' => $deliveryPerson,
                'delivery_contact' => $contact,
                'delivery_address' => $address,
                'delivery_status' => $status,
                'signature_recipient_name' => $signatureRecipientName,
                'signature_data' => $signatureData,
                'delivery_confirmation' => $deliveryConfirmation,
                'temperature_check' => $temperatureCheck,
                'quality_check' => $qualityCheck,
                'additional_notes' => $additionalNotes,
                'delivery_photos' => null,
                'customer_rating' => $customerRating,
                'customer_comments' => $customerComments,
                'customer_complaints' => null,
                'feedback_photos' => null,
                'feedback_submitted_at' => $feedbackSubmittedAt,
                'feedback_status' => $feedbackStatus,
                'admin_notes' => null,
            ]);
        }

        $this->command->info('Deliveries seeded successfully! Created ' . Delivery::count() . ' deliveries.');
    }

    /**
     * Select a random index based on weights
     */
    private function weightedRandom(array $weights): int
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        
        $sum = 0;
        foreach ($weights as $index => $weight) {
            $sum += $weight;
            if ($random <= $sum) {
                return $index;
            }
        }
        
        return 0;
    }
}
