<?php

namespace Database\Factories;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnquiryFactory extends Factory
{
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

    public function unworked()
    {
        return $this->state(['status' => EnquiryStatus::New]);
    }
}
