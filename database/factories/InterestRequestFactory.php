<?php

namespace Database\Factories;

use App\Models\InterestRequest;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InterestRequest> */
class InterestRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'venue_id' => Venue::factory(),
            'status' => fake()->randomElement(['pending', 'contacted', 'done']),
            'admin_note' => null,
            'responded_at' => null,
        ];
    }
}
