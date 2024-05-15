<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        return [
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'recipient_id' => $recipient->id,
            'recipient_name' => $recipient->name,
            'message' => fake()->realText(50),
            'replied_to' => null,
        ];
    }
}
