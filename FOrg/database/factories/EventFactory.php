<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $client = Client::factory()->create();
        $author = User::factory()->create();
        $author->assignRole('organizer');


        return [
            'location' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'client_id' => $client->id,
            'user_id' => $author->id,
            'start_date' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'end_date' => fake()->dateTimeBetween('+1 week', '+3 week'),
            'event_type' => Arr::random(['Private', 'Public', 'Rent Order']),
            'event_subtype' => Arr::random(['Wedding', 'Birthday', 'Corporate', 'Concert', 'Town Celebration', 'Holiday Related', 'Other']),
            'description' => fake()->text(50),
            'event_status' => 'Draft',
        ];
    }
}
