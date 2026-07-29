<?php

namespace Database\Factories;

use App\Enums\CarStatus;
use App\Filament\Resources\Cars\Schemas\CarForm;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    public function definition(): array
    {
        $model = fake()->randomElement(['M4', 'RS7', 'Model S', 'Mustang', 'RAV4']);
        $year = fake()->numberBetween(2018, 2025);
        $title = "Demo {$model}";

        return [
            'slug' => Str::slug("{$year} {$title}").'-'.fake()->unique()->numberBetween(1, 99999),
            'title' => $title,
            'brand_id' => Brand::factory(),
            'model' => $model,
            'year' => $year,
            'body_type' => fake()->randomElement(CarForm::BODY_TYPES),
            'condition' => fake()->randomElement(CarForm::CONDITIONS),
            'status' => CarStatus::Available,
            'city' => fake()->city(),
            'state' => fake()->state(),
            'price' => fake()->numberBetween(20_000_000, 400_000_000),
            'mileage' => fake()->numberBetween(0, 90000),
            'transmission' => fake()->randomElement(CarForm::TRANSMISSIONS),
            'fuel_type' => fake()->randomElement(CarForm::FUEL_TYPES),
            'seats' => fake()->numberBetween(2, 7),
            'horsepower' => fake()->numberBetween(100, 700),
            'exterior_color' => fake()->safeColorName(),
            'badge' => fake()->optional()->randomElement(CarForm::BADGES),
            'image' => 'cars/example.jpg',
            'gallery' => ['cars/example.jpg'],
            'features' => ['Heated Seats', 'Apple CarPlay'],
            'description' => fake()->paragraph(),
            'is_featured' => fake()->boolean(30),
        ];
    }
}
