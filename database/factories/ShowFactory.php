<?php

namespace Database\Factories;

use App\Models\Regency;
use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Show> */
class ShowFactory extends Factory
{
    public function definition(): array
    {
        $title = 'Pertunjukan '.fake()->unique()->words(2, true);

        return [
            'title' => str($title)->title(),
            'slug' => str($title)->slug(),
            'description' => fake()->paragraph(),
            'starts_at' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'location_name' => fake()->city(),
            'regency_id' => Regency::factory(),
            'ticket_price' => fake()->randomElement([0, 25000, 50000, 100000]),
            'organizer' => fake()->company(),
        ];
    }
}
