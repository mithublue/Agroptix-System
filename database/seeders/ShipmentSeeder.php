<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Shipment;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing shipments
        Shipment::truncate();

        // Get all batches
        $batches = Batch::all();

        if ($batches->isEmpty()) {
            $this->command->warn('No batches found. Please run TestDataSeeder first.');
            return;
        }

        $origins = [
            'Dhaka, Bangladesh',
            'Chittagong, Bangladesh',
            'Sylhet, Bangladesh',
            'Rajshahi, Bangladesh',
            'Khulna, Bangladesh',
            'Rangpur, Bangladesh',
            'Mymensingh, Bangladesh',
        ];

        $destinations = [
            'London, UK',
            'New York, USA',
            'Dubai, UAE',
            'Singapore',
            'Tokyo, Japan',
            'Sydney, Australia',
            'Toronto, Canada',
            'Paris, France',
            'Berlin, Germany',
            'Mumbai, India',
        ];

        $vehicleTypes = ['truck', 'ship', 'air_freight', 'rail', 'refrigerated_truck'];
        $fuelTypes = ['diesel', 'electric', 'hybrid', 'cng', 'biodiesel'];
        $modes = ['road', 'sea', 'air', 'rail', 'multimodal'];

        // Create shipments for 70% of batches
        foreach ($batches->random(min((int)($batches->count() * 0.7), $batches->count())) as $batch) {
            $origin = $origins[array_rand($origins)];
            $destination = $destinations[array_rand($destinations)];
            $vehicleType = $vehicleTypes[array_rand($vehicleTypes)];
            $fuelType = $fuelTypes[array_rand($fuelTypes)];
            $mode = $modes[array_rand($modes)];
            
            // Calculate route distance (50-5000 km)
            $routeDistance = rand(50, 5000);
            
            // Calculate CO2 estimate based on distance and mode
            $co2PerKm = match($mode) {
                'air' => 0.5,
                'road' => 0.2,
                'rail' => 0.05,
                'sea' => 0.01,
                'multimodal' => 0.15,
                default => 0.2,
            };
            
            $co2Estimate = round($routeDistance * $co2PerKm, 2);
            
            // Generate departure time (within last 60 days)
            $departureTime = now()->subDays(rand(1, 60))->subHours(rand(0, 23));
            
            // Generate arrival time (1-30 days after departure based on mode)
            $transitDays = match($mode) {
                'air' => rand(1, 3),
                'road' => rand(1, 7),
                'rail' => rand(2, 10),
                'sea' => rand(10, 30),
                'multimodal' => rand(5, 20),
                default => rand(3, 14),
            };
            
            $arrivalTime = $departureTime->copy()->addDays($transitDays)->addHours(rand(0, 23));
            
            // Temperature range for perishable goods
            $temperature = $batch->product->is_perishable ? rand(-5, 15) : rand(10, 30);

            Shipment::create([
                'batch_id' => $batch->id,
                'origin' => $origin,
                'destination' => $destination,
                'vehicle_type' => $vehicleType,
                'co2_estimate' => $co2Estimate,
                'departure_time' => $departureTime,
                'arrival_time' => $arrivalTime,
                'fuel_type' => $fuelType,
                'temperature' => $temperature,
                'mode' => $mode,
                'route_distance' => $routeDistance,
            ]);
        }

        $this->command->info('Shipments seeded successfully! Created ' . Shipment::count() . ' shipments.');
    }
}
