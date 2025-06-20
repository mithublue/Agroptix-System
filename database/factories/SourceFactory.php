<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\OnDelete('cascade')>onUpdate('cascade');
use App\Models\Source;
use App\Models\UserAsOwner;

class SourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Source::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => fake()->regexify('[A-Za-z0-9]{20}'),
            'gps_lat' => fake()->word(),
            'gps_long' => fake()->word(),
            'production_method' => fake()->regexify('[A-Za-z0-9]{20}'),
            'area' => fake()->word(),
            'status' => fake()->regexify('[A-Za-z0-9]{50}'),
            'owner_id' => OnDelete('cascade')>onUpdate('cascade')::factory(),
            'user_as_owner_id' => UserAsOwner::factory(),
        ];
    }
}
