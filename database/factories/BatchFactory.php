<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Batch;
use App\Models\ProductAsProduct;
use App\Models\Products,;
use App\Models\SourceAsSource;
use App\Models\Sources,;

class BatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Batch::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'batch_code' => fake()->word(),
            'source_id' => Sources,::factory(),
            'product_id' => Products,::factory(),
            'harvest_time' => fake()->word(),
            'status' => fake()->regexify('[A-Za-z0-9]{20}'),
            'source_as_source_id' => SourceAsSource::factory(),
            'product_as_product_id' => ProductAsProduct::factory(),
        ];
    }
}
