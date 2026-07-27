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
class EnquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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

    /**
     * Mark the enquiry as unworked (status = New).
     *
     * @return self
     */
    public function unworked(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnquiryStatus::New,
        ]);
    }
}
