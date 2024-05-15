<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'owner' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'audio_subrent' => fake()->boolean(),
            'video_subrent' => fake()->boolean(),
            'vehicle_subrent' => fake()->boolean(),
            'storage_subrent' => fake()->boolean(),
            'stage_subrent' => fake()->boolean()
        ];
    }
}
