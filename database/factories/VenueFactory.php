<?php

namespace Database\Factories;

use App\Models\Regency;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Venue> */
class VenueFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Sanggar '.fake()->unique()->city();

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'type' => fake()->randomElement(['sanggar_tari', 'gamelan', 'karawitan', 'komunitas', 'lainnya']),
            'address' => fake()->streetAddress(),
            'regency_id' => Regency::factory(),
            'phone' => fake()->phoneNumber(),
            'description' => fake()->sentence(),
        ];
    }
}
