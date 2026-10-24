<?php

namespace Database\Factories;

use App\Models\Island;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Island> */
class IslandFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Sumatera', 'Jawa', 'Kalimantan', 'Sulawesi', 'Papua', 'Bali Nusa Tenggara', 'Maluku']);

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'description' => fake()->sentence(),
        ];
    }
}
