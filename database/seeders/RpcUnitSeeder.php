<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RpcUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rpcUnits = [
            [
                'rpc_identifier' => 'RPC-'.strtoupper(uniqid()),
                'capacity_kg' => 1000.50,
                'material_type' => 'Plastic HDPE',
                'initial_purchase_date' => now()->subYear(),
                'last_washed_date' => now()->subWeek(),
                'total_wash_cycles' => 15,
                'total_reuse_count' => 30,
                'current_location' => 'Warehouse A, Shelf 3',
                'status' => 'available',
            ],
            [
                'rpc_identifier' => 'RPC-'.strtoupper(uniqid()),
                'capacity_kg' => 800.00,
                'material_type' => 'Plastic PP',
                'initial_purchase_date' => now()->subMonths(6),
                'last_washed_date' => now()->subDay(),
                'total_wash_cycles' => 8,
                'total_reuse_count' => 12,
                'current_location' => 'Loading Dock 2',
                'status' => 'in_use',
            ],
            [
                'rpc_identifier' => 'RPC-'.strtoupper(uniqid()),
                'capacity_kg' => 1200.00,
                'material_type' => 'Metal',
                'initial_purchase_date' => now()->subYears(2),
                'last_washed_date' => now()->subDays(3),
                'total_wash_cycles' => 45,
                'total_reuse_count' => 90,
                'current_location' => 'Repair Area',
                'status' => 'in_repair',
            ],
            [
                'rpc_identifier' => 'RPC-'.strtoupper(uniqid()),
                'capacity_kg' => 500.00,
                'material_type' => 'Wood',
                'initial_purchase_date' => now()->subYears(3),
                'last_washed_date' => now()->subMonths(2),
                'total_wash_cycles' => 60,
                'total_reuse_count' => 120,
                'current_location' => 'Retirement Storage',
                'status' => 'retired',
            ],
        ];

        foreach ($rpcUnits as $rpcUnit) {
            \App\Models\RpcUnit::create($rpcUnit);
        }
    }
}
