<?php

namespace Database\Factories;

use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'title' => $this->faker->sentence(3),
            'brand' => $this->faker->word(),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'price' => $this->faker->numberBetween(1_000_000, 100_000_000),
            'mileage' => $this->faker->numberBetween(0, 200_000),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'body_type' => $this->faker->word(),
            'transmission' => $this->faker->randomElement(['manual', 'automatic']),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel', 'hybrid']),
            'condition' => $this->faker->randomElement(['new', 'used']),
            'status' => CarStatus::Available,
            'is_featured' => false,
            'gallery' => [],
            'features' => [],
        ];
    }
}
