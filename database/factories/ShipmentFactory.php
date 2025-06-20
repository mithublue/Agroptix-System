<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\BatchAsBatch;
use App\Models\Batches,;
use App\Models\Shipment;

class ShipmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shipment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'batch_id' => Batches,::factory(),
            'origin' => fake()->word(),
            'destination' => fake()->word(),
            'vehicle_type' => fake()->word(),
            'co2_estimate' => fake()->randomFloat(2, 0, 999999.99),
            'departure_time' => fake()->word(),
            'arrival_time' => fake()->word(),
            'batch_as_batch_id' => BatchAsBatch::factory(),
        ];
    }
}
