<?php

namespace Database\Factories;

use App\Models\Island;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Province> */
class ProvinceFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'island_id' => Island::factory(),
            'name' => $name,
            'slug' => str($name)->slug(),
            'description' => fake()->sentence(),
        ];
    }
}
