<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ComprehensiveTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder orchestrates all test data creation in the correct order
     * to ensure proper relationships and data integrity.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive test data seeding...');
        $this->command->newLine();

        // Step 1: Users and Permissions
        $this->command->info('Step 1/9: Seeding users and permissions...');
        $this->call([
            TestUsersSeeder::class,
        ]);
        $this->command->newLine();

        // Step 1b: Compliance Standards
        $this->command->info('Step 1b: Seeding compliance standards...');
        $this->call([
            ComplianceStandardSeeder::class,
        ]);
        $this->command->newLine();

        // Step 2: Core Data (Sources, Products, Batches, Quality Tests)
        $this->command->info('Step 2/9: Seeding core data (sources, products, batches, quality tests)...');
        $this->call([
            TestDataSeeder::class,
        ]);
        $this->command->newLine();

        // Step 2b: Certifications
        $this->command->info('Step 2b: Seeding certifications...');
        $this->call([
            CertificationSeeder::class,
        ]);
        $this->command->newLine();

        // Step 3: Shipments
        $this->command->info('Step 3/9: Seeding shipments...');
        $this->call([
            ShipmentSeeder::class,
        ]);
        $this->command->newLine();

        // Step 4: Eco Processes
        $this->command->info('Step 4/9: Seeding eco processes...');
        $this->call([
            EcoProcessSeeder::class,
        ]);
        $this->command->newLine();

        // Step 5: Deliveries
        $this->command->info('Step 5/9: Seeding deliveries...');
        $this->call([
            DeliverySeeder::class,
        ]);
        $this->command->newLine();

        // Step 6: Conversations and Messages
        $this->command->info('Step 6/9: Seeding conversations and messages...');
        $this->call([
            ConversationSeeder::class,
        ]);
        $this->command->newLine();

        // Step 7: Trace Events
        $this->command->info('Step 7/9: Seeding trace events...');
        $this->call([
            TraceEventsSeeder::class,
        ]);
        $this->command->newLine();

        // Step 8: RPC Units
        $this->command->info('Step 8/9: Seeding RPC units...');
        $this->call([
            RpcUnitSeeder::class,
        ]);
        $this->command->newLine();

        // Step 9: Packaging
        $this->command->info('Step 9/9: Seeding packaging data...');
        $this->call([
            PackagingSeeder::class,
        ]);
        $this->command->newLine();

        $this->command->info('✓ Comprehensive test data seeding completed successfully!');
        $this->command->newLine();

        // Display summary
        $this->displaySummary();
    }

    /**
     * Display a summary of seeded data
     */
    private function displaySummary(): void
    {
        $this->command->info('=== Data Summary ===');

        $models = [
            'Users' => \App\Models\User::class,
            'Sources' => \App\Models\Source::class,
            'Products' => \App\Models\Product::class,
            'Batches' => \App\Models\Batch::class,
            'Quality Tests' => \App\Models\QualityTest::class,
            'Shipments' => \App\Models\Shipment::class,
            'Eco Processes' => \App\Models\EcoProcess::class,
            'Deliveries' => \App\Models\Delivery::class,
            'Conversations' => \App\Models\Conversation::class,
            'Messages' => \App\Models\Message::class,
            'Trace Events' => \App\Models\TraceEvent::class,
            'RPC Units' => \App\Models\RpcUnit::class,
            'Packaging' => \App\Models\Packaging::class,
            'Compliance Standards' => \App\Models\ComplianceStandard::class,
            'Certifications' => \App\Models\Certification::class,
        ];

        foreach ($models as $label => $modelClass) {
            if (class_exists($modelClass)) {
                $count = $modelClass::count();
                $this->command->info(sprintf('%-20s: %d records', $label, $count));
            }
        }

        $this->command->newLine();
        $this->command->info('Your development environment is now fully seeded with test data!');
    }
}
