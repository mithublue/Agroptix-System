<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Product;
use App\Models\QualityTest;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear existing data
        QualityTest::truncate();
        Batch::truncate();
        Product::truncate();
        Source::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create sources
        $sources = [
            [
                'type' => 'perishable',
                'gps_lat' => '23.8103',
                'gps_long' => '90.4125',
                'production_method' => 'organic',
                'area' => '10 acres',
                'status' => 'active',
                'owner_id' => $user->id
            ],
            [
                'type' => 'non_perishable',
                'gps_lat' => '23.8110',
                'gps_long' => '90.4130',
                'production_method' => 'conventional',
                'area' => '25 acres',
                'status' => 'active',
                'owner_id' => $user->id
            ],
            [
                'type' => 'perishable',
                'gps_lat' => '23.8120',
                'gps_long' => '90.4140',
                'production_method' => 'conventional',
                'area' => 'N/A',
                'status' => 'active',
                'owner_id' => $user->id
            ],
            [
                'type' => 'non_perishable',
                'gps_lat' => '23.8090',
                'gps_long' => '90.4110',
                'production_method' => 'organic',
                'area' => '15 acres',
                'status' => 'pending',
                'owner_id' => $user->id
            ]
        ];

        foreach ($sources as $sourceData) {
            Source::create($sourceData);
        }

        // Create products
        $products = [
            [
                'name' => 'Mango',
                'type' => 'fruit',
                'is_perishable' => '1',
                'hs_code' => '080450'
            ],
            [
                'name' => 'Jackfruit',
                'type' => 'fruit',
                'is_perishable' => '1',
                'hs_code' => '081090'
            ],
            [
                'name' => 'Pineapple',
                'type' => 'fruit',
                'is_perishable' => '1',
                'hs_code' => '080430'
            ],
            [
                'name' => 'Banana',
                'type' => 'fruit',
                'is_perishable' => '1',
                'hs_code' => '080390'
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Get all sources and products
        $sources = Source::all();
        $products = Product::all();

        // Create batches
        $batches = [];
        $statuses = ['pending', 'in_storage', 'in_transit', 'delivered', 'cancelled'];
        
        for ($i = 0; $i < 20; $i++) {
            $source = $sources->random();
            $product = $products->random();
            
            // Generate a random harvest date within the last 30 days
            $harvestTime = now()->subDays(rand(1, 30))->toDateString();
            
            $batches[] = [
                'source_id' => $source->id,
                'product_id' => $product->id,
                'batch_code' => 'B' . now()->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'harvest_time' => $harvestTime,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Insert batches in chunks for better performance
        foreach (array_chunk($batches, 10) as $chunk) {
            Batch::insert($chunk);
        }

        // Get all batches
        $batches = Batch::all();
        $testParameters = ['e_coli', 'salmonella', 'brix', 'firmness', 'pesticide_residues', 'aspergillus', 'co2_level', 'ph', 'heavy_metals'];
        
        // Create quality tests for some batches
        foreach ($batches->random(15) as $batch) {
            // Randomly select 3-5 test parameters
            $selectedParams = array_rand(array_flip($testParameters), rand(3, 5));
            if (!is_array($selectedParams)) {
                $selectedParams = [$selectedParams];
            }
            
            // Prepare result status
            $results = [];
            foreach ($selectedParams as $param) {
                $results[$param . '_result'] = $this->generateTestResult($param);
            }
            
            // Create quality tests
            $test = QualityTest::create([
                'batch_id' => $batch->id,
                'user_id' => 1, // Assuming user with ID 1 exists
                'parameter_tested' => json_encode($selectedParams),
                'result' => $this->getOverallResult($results),
                'result_status' => json_encode($results),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    /**
     * Generate random test results based on parameter type
     */
    private function generateTestResult(string $param): mixed
    {
        return match($param) {
            'e_coli' => rand(0, 100) < 90 ? 'negative' : 'positive',
            'salmonella' => rand(0, 100) < 95 ? 'negative' : 'positive',
            'brix' => rand(8, 25) + (rand(0, 9) / 10), // 8.0 to 25.9
            'firmness' => rand(1, 10) + (rand(0, 9) / 10), // 1.0 to 10.9
            'pesticide_residues' => rand(0, 5) / 100, // 0.00 to 0.05
            'aspergillus' => rand(0, 100) < 85 ? 'negative' : 'positive',
            'co2_level' => rand(300, 2000), // ppm
            'ph' => rand(30, 80) / 10, // 3.0 to 8.0
            'heavy_metals' => rand(0, 5) / 1000, // 0.000 to 0.005
            default => null,
        };
    }
    
    /**
     * Determine the overall test result based on individual test results
     */
    private function getOverallResult(array $results): string
    {
        // If any test is in a failed state, the overall result is 'fail'
        foreach ($results as $param => $value) {
            if (str_contains($param, 'e_coli') && $value === 'positive') {
                return 'fail';
            }
            
            if (str_contains($param, 'salmonella') && $value === 'positive') {
                return 'fail';
            }
            
            if (str_contains($param, 'pesticide_residues') && $value > 0.05) {
                return 'fail';
            }
            
            if (str_contains($param, 'ph') && ($value < 3.5 || $value > 8.5)) {
                return 'fail';
            }
            
            if (str_contains($param, 'heavy_metals') && $value > 0.005) {
                return 'fail';
            }
            
            if (str_contains($param, 'aspergillus') && $value === 'positive') {
                return 'fail';
            }
        }
        
        // If no failures, check for warning conditions
        foreach ($results as $param => $value) {
            if ((str_contains($param, 'pesticide_residues') && $value > 0.03) ||
                (str_contains($param, 'ph') && ($value < 4.0 || $value > 8.0)) ||
                (str_contains($param, 'heavy_metals') && $value > 0.003)) {
                return 'warning';
            }
        }
        
        // If no failures or warnings, return pass
        return 'pass';
    }
}
