<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\EcoProcess;
use Illuminate\Database\Seeder;

class EcoProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing eco processes
        EcoProcess::truncate();

        // Get all batches
        $batches = Batch::all();

        if ($batches->isEmpty()) {
            $this->command->warn('No batches found. Please run TestDataSeeder first.');
            return;
        }

        $stages = [
            'harvesting' => [
                'weather_conditions' => ['sunny', 'cloudy', 'rainy'],
                'workers_count' => [5, 10, 15, 20],
                'equipment_used' => ['manual', 'semi-automated', 'automated'],
            ],
            'cleaning' => [
                'water_usage_liters' => [100, 200, 500, 1000],
                'cleaning_method' => ['water_wash', 'dry_clean', 'steam_clean'],
                'duration_minutes' => [30, 60, 90, 120],
            ],
            'sorting' => [
                'quality_grade' => ['A', 'B', 'C'],
                'rejection_rate_percent' => [5, 10, 15, 20],
                'sorting_method' => ['manual', 'optical', 'weight-based'],
            ],
            'packaging' => [
                'package_type' => ['cardboard_box', 'plastic_crate', 'wooden_crate', 'vacuum_sealed'],
                'packages_count' => [10, 20, 50, 100],
                'labeling_method' => ['manual', 'automated'],
            ],
            'storage' => [
                'temperature_celsius' => [-5, 0, 5, 10, 15, 20],
                'humidity_percent' => [40, 50, 60, 70, 80],
                'storage_duration_hours' => [24, 48, 72, 120, 168],
            ],
            'quality_inspection' => [
                'inspector_name' => ['John Doe', 'Jane Smith', 'Ahmed Ali', 'Maria Garcia'],
                'inspection_result' => ['passed', 'passed_with_notes', 'failed'],
                'samples_tested' => [5, 10, 15, 20],
            ],
        ];

        $statuses = ['pending', 'in_progress', 'completed', 'failed'];

        // Create eco processes for each batch
        foreach ($batches as $batch) {
            $baseTime = $batch->created_at;
            $processCount = rand(3, 6); // 3-6 processes per batch

            // Randomly select stages
            $selectedStages = array_rand($stages, $processCount);
            if (!is_array($selectedStages)) {
                $selectedStages = [$selectedStages];
            }

            foreach ($selectedStages as $index => $stageName) {
                $stageConfig = $stages[$stageName];

                // Generate random data for this stage
                $data = [];
                foreach ($stageConfig as $key => $options) {
                    $data[$key] = $options[array_rand($options)];
                }

                // Add common data fields
                $data['notes'] = 'Process completed as per standard operating procedures.';
                $data['operator'] = 'Operator ' . rand(1, 10);

                // Calculate start and end times
                $startTime = $baseTime->copy()->addHours($index * rand(2, 8));
                $duration = rand(1, 6); // 1-6 hours
                $endTime = $startTime->copy()->addHours($duration);

                // Determine status (90% completed, 5% in_progress, 5% failed)
                $statusRand = rand(1, 100);
                if ($statusRand <= 90) {
                    $status = 'completed';
                } elseif ($statusRand <= 95) {
                    $status = 'in_progress';
                    $endTime = null; // In progress processes don't have end time
                } else {
                    $status = 'failed';
                    $data['failure_reason'] = 'Equipment malfunction';
                }

                // Calculate random resource usage
                $waterUsage = rand(50, 500); // Liters
                $energyUsage = rand(10, 100); // kWh
                $wasteGenerated = rand(1, 50); // Kg

                EcoProcess::create([
                    'batch_id' => $batch->id,
                    'stage' => $stageName,
                    'data' => $data,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $status,
                    'water_usage' => $waterUsage,
                    'energy_usage' => $energyUsage,
                    'waste_generated' => $wasteGenerated,
                ]);
            }
        }

        $this->command->info('Eco processes seeded successfully! Created ' . EcoProcess::count() . ' eco processes.');
    }
}
