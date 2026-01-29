<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplianceStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $standards = [
            // EU Standards for Tomato
            [
                'region' => 'EU',
                'crop_type' => 'Tomato',
                'parameter_name' => 'pesticide_residue',
                'max_value' => 0.01,
                'unit' => 'mg/kg',
                'critical_action' => 'reject_batch',
            ],
            [
                'region' => 'EU',
                'crop_type' => 'Tomato',
                'parameter_name' => 'nitrate',
                'max_value' => 150,
                'unit' => 'mg/kg',
                'critical_action' => 'warning',
            ],

            // GCC Standards for Lettuce
            [
                'region' => 'GCC',
                'crop_type' => 'Lettuce',
                'parameter_name' => 'e_coli',
                'max_value' => 0, // Zero tolerance
                'unit' => 'CFU/g',
                'critical_action' => 'reject_batch',
            ],
            [
                'region' => 'GCC',
                'crop_type' => 'Lettuce',
                'parameter_name' => 'heavy_metals_lead',
                'max_value' => 0.3,
                'unit' => 'mg/kg',
                'critical_action' => 'reject_batch',
            ],

            // US Standards (Generic)
            [
                'region' => 'US',
                'crop_type' => 'Tomato',
                'parameter_name' => 'salmonella',
                'max_value' => 0,
                'unit' => 'detectable/25g',
                'critical_action' => 'reject_batch',
            ],
        ];

        foreach ($standards as $standard) {
            \App\Models\ComplianceStandard::create($standard);
        }
    }
}
