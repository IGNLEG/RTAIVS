<?php

namespace Http\Controllers;

use App\Models\Event;
use App\Models\EventMessage;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class EventMessageControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $sender;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->sender = User::factory()->create();
        $this->sender->assignRole('user');
    }

    public function test_if_user_gets_all_event_messages()
    {
        $this->actingAs($this->sender);
        $event = Event::factory()->create();
        EventMessage::factory(10)->create([
            'event_id' => $event->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name
        ]);

        $response = $this->get("/api/events/$event->id/messages");

        $response->assertStatus(200);
        $response->assertJsonCount(10);
    }

    public function test_if_user_can_create_event_message()
    {
        $this->actingAs($this->sender);
        $event = Event::factory()->create();

        $response = $this->post("/api/events/$event->id/messages",[
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message' => 'hallo'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('event_messages',[
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'event_id' => $event->id,
            'message' => 'hallo'
        ]);
    }

    public function test_if_user_can_delete_his_event_message()
    {
        $this->actingAs($this->sender);
        $event = Event::factory()->create();
        $message = EventMessage::factory()->create([
            'event_id' => $event->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name
        ]);

        $response = $this->delete("/api/events/$event->id/messages/$message->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('event_messages',[
            'id' => $message->id,
            'sender_id' => $message->sender->id,
            'sender_name' => $message->sender->name,
            'event_id' => $message->event_id,
            'message' => $message->message
        ]);
    }
}
