<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing certifications
        Certification::truncate();

        $sources = Source::all();

        if ($sources->isEmpty()) {
            $this->command->warn('No sources found. Skipping certification seeding.');
            return;
        }

        $certTypes = [
            'GlobalGAP',
            'Organic EU',
            'USDA Organic',
            'FairTrade',
            'Rainforest Alliance',
            'ISO 22000'
        ];

        $issuers = [
            'Bureau Veritas',
            'SGS',
            'Control Union',
            'TUV Nord',
            'Kiwa'
        ];

        foreach ($sources as $source) {
            // Assign 1-3 certifications per source
            $numCerts = rand(1, 3);

            // Randomly select distinct cert types
            $selectedTypes = collect($certTypes)->random($numCerts);

            foreach ($selectedTypes as $type) {
                // Determine status based on dates
                // Most valid, some expired
                $isValid = rand(1, 10) > 1; // 90% valid

                if ($isValid) {
                    $issueDate = Carbon::now()->subMonths(rand(1, 11));
                    $expiryDate = Carbon::now()->addMonths(rand(1, 12));
                    $status = 'active';
                } else {
                    $issueDate = Carbon::now()->subMonths(rand(13, 24));
                    $expiryDate = Carbon::now()->subMonths(rand(1, 5));
                    $status = 'expired';
                }

                Certification::create([
                    'source_id' => $source->id,
                    'type' => $type,
                    'document_path' => 'certifications/demo_cert.pdf', // Dummy path
                    'certifying_body' => $issuers[array_rand($issuers)],
                    'issue_date' => $issueDate,
                    'expiry_date' => $expiryDate,
                    'is_active' => $status === 'active',
                    'is_verified' => rand(0, 1),
                    'verified_by' => 1, // Admin user
                    'verified_at' => Carbon::now(),
                    'verification_notes' => 'Seeded data for testing compliance.',
                ]);
            }
        }

        $this->command->info('Certifications seeded successfully! Created ' . Certification::count() . ' certifications.');
    }
}
