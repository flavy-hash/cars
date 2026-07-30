<?php

namespace Database\Factories;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Models\Car;
use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
use Illuminate\Database\Eloquent\Factories\Factory;
class EnquiryFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(EnquiryType::cases());

        return [
            'car_id' => Car::factory(),
            'type' => $type,
            'status' => fake()->randomElement(EnquiryStatus::cases()),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('+1 555 ### ####'),
            'message' => fake()->optional()->sentence(),
            'preferred_at' => $type->needsPreferredDate() ? fake()->dateTimeBetween('+1 day', '+3 weeks') : null,
            'admin_notes' => null,
        ];
    }

    /** Not named new() — that would clash with Factory::new(). */
    public function unworked(): static

        return [
            'car_id' => Car::factory(),
            'type' => EnquiryType::Reservation,
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'message' => $this->faker->sentence(),
            'status' => EnquiryStatus::New,
            'preferred_at' => null,
        ];
    }

    public function unworked()
    {
        return $this->state(['status' => EnquiryStatus::New]);
    }
}
