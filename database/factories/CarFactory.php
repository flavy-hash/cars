<?php

namespace Database\Factories;

use App\Enums\CarStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'brand' => $this->faker->word(),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'price' => $this->faker->numberBetween(5_000_000, 100_000_000),
            'mileage' => $this->faker->numberBetween(0, 200_000),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'body_type' => $this->faker->word(),
            'transmission' => $this->faker->randomElement(['manual', 'automatic']),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel']),
            'condition' => $this->faker->randomElement(['new', 'used']),
            'slug' => $this->faker->slug(),
            'status' => CarStatus::Available,
            'is_featured' => false,
            'gallery' => [],
            'features' => [],
        ];
    }
}
