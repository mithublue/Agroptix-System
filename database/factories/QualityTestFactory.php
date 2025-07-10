<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Batch;
use App\Models\QualityTest;
use App\Models\User;

class QualityTestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QualityTest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'batch_id' => Batch::factory(),
            'user_id' => User::factory(),
            'parameter_tested' => fake()->regexify('[A-Za-z0-9]{50}'),
            'result' => fake()->regexify('[A-Za-z0-9]{100}'),
            'result_status' => fake()->regexify('[A-Za-z0-9]{10}'),
            'batch_as_batch_id' => null,
            'user_as_user_id' => null,
        ];
    }
}
