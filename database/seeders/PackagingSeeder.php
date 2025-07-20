<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackagingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some RPC units to associate with packaging
        $rpcUnits = \App\Models\RpcUnit::take(3)->get();
        
        // Get or create a batch to associate with packaging
        $batch = \App\Models\Batch::first();
        if (!$batch) {
            // First ensure we have a source and product
            $source = \App\Models\Source::first();
            if (!$source) {
                // Get or create an owner user
                $owner = \App\Models\User::where('email', 'supplier@example.com')->first();
                if (!$owner) {
                    $owner = \App\Models\User::create([
                        'name' => 'Supplier User',
                        'email' => 'supplier@example.com',
                        'password' => bcrypt('password'),
                        'phone' => '1234567890',
                        'email_verified_at' => now(),
                    ]);
                }
                
                $source = \App\Models\Source::create([
                    'type' => 'farm',
                    'gps_lat' => '12.3456',
                    'gps_long' => '98.7654',
                    'production_method' => 'organic',
                    'area' => '1000 sqm',
                    'status' => 'active',
                    'owner_id' => $owner->id,
                ]);
            }
            
            $product = \App\Models\Product::first();
            if (!$product) {
                $product = \App\Models\Product::create([
                    'name' => 'Test Product',
                    'description' => 'Test Product Description',
                    'price' => 99.99,
                ]);
            }
            
            $batch = \App\Models\Batch::create([
                'source_id' => $source->id,
                'product_id' => $product->id,
                'harvest_time' => now()->toDateTimeString(),
                'status' => 'pending',
                'batch_code' => 'BATCH-' . strtoupper(uniqid()),
            ]);
        }
        
        // Get or create a user to be the packer
        $packer = \App\Models\User::first();
        if (!$packer) {
            $packer = \App\Models\User::create([
                'name' => 'Packaging Operator',
                'email' => 'packager@example.com',
                'password' => bcrypt('password'),
                'phone' => '1234567890',
                'email_verified_at' => now(),
            ]);
        }
        
        $packagingData = [
            [
                'batch_id' => $batch->id,
                'qr_code' => 'PKG-' . strtoupper(uniqid()),
                'package_type' => 'Box',
                'material_type' => 'Cardboard',
                'unit_weight_packaging' => 250.5,
                'total_product_weight' => 5000.0,
                'total_package_weight' => 5250.5,
                'quantity_of_units' => 20,
                'packaging_start_time' => now()->subHours(2),
                'packaging_end_time' => now()->subHour(),
                'packaging_location' => 'Packing Station 1',
                'packer_id' => $packer->id,
                'rpc_unit_id' => $rpcUnits->isNotEmpty() ? $rpcUnits->first()->id : null,
                'cleanliness_checklist' => true,
                'co2_estimate' => 2.5,
            ],
            [
                'batch_id' => $batch->id,
                'qr_code' => 'PKG-' . strtoupper(uniqid()),
                'package_type' => 'Crate',
                'material_type' => 'Plastic',
                'unit_weight_packaging' => 500.0,
                'total_product_weight' => 10000.0,
                'total_package_weight' => 10500.0,
                'quantity_of_units' => 15,
                'packaging_start_time' => now()->subDays(1),
                'packaging_end_time' => now()->subHours(23),
                'packaging_location' => 'Packing Station 2',
                'packer_id' => $packer->id,
                'rpc_unit_id' => $rpcUnits->count() > 1 ? $rpcUnits[1]->id : null,
                'cleanliness_checklist' => true,
                'co2_estimate' => 1.8,
            ],
            [
                'batch_id' => $batch->id,
                'qr_code' => 'PKG-' . strtoupper(uniqid()),
                'package_type' => 'Pallet',
                'material_type' => 'Wood',
                'unit_weight_packaging' => 2000.0,
                'total_product_weight' => 20000.0,
                'total_package_weight' => 22000.0,
                'quantity_of_units' => 100,
                'packaging_start_time' => now()->subWeek(),
                'packaging_end_time' => now()->subWeek()->addHours(2),
                'packaging_location' => 'Warehouse',
                'packer_id' => $packer->id,
                'rpc_unit_id' => $rpcUnits->count() > 2 ? $rpcUnits[2]->id : null,
                'cleanliness_checklist' => false,
                'co2_estimate' => 5.2,
            ],
        ];
        
        foreach ($packagingData as $data) {
            \App\Models\Packaging::create($data);
        }
    }
}
