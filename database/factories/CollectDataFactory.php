<?php

namespace Database\Factories;

use App\Models\CollectData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CollectData>
 */
class CollectDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CollectData::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'temperature' => $this->faker->numberBetween(20, 40),
            'air_humidity' => $this->faker->numberBetween(30, 70),
            'soil_humidity' => $this->faker->numberBetween(10, 50),
            'light' => $this->faker->numberBetween(100, 1000),
            'created_at' => now()->subDays(rand(0, 50)),
            'updated_at' => now()->addDays(rand(0, 50)),
        ];
    }
}
