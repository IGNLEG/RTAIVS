<?php

namespace Http\Controllers;

use App\Http\Controllers\MessageController;
use App\Models\Message;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $sender;
    private User $recipient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->sender = User::factory()->create();
        $this->sender->assignRole('user');
        $this->recipient = User::factory()->create();
        $this->recipient->assignRole('user');
    }

    public function test_if_user_can_send_or_reply_to_message()
    {
        $this->actingAs($this->sender);

        $response = $this->post('/api/inbox',[
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);

        $this->actingAs($this->recipient);

        $response = $this->post('/api/inbox',[
            'recipient_name' => $this->sender->name,
            'message' => 'ello govna',
            'replied_to' => 'Hello'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->recipient->id,
            'sender_name' => $this->recipient->name,
            'recipient_id' => $this->sender->id,
            'recipient_name' => $this->sender->name,
            'message' => 'ello govna',
            'replied_to' => 'Hello'
        ]);
    }

    public function test_if_user_can_get_his_message()
    {
        $this->actingAs($this->sender);

        $response = $this->post('/api/inbox',[
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);

        $this->actingAs($this->recipient);

        $response = $this->post('/api/inbox',[
            'recipient_name' => $this->sender->name,
            'message' => 'ello govna',
            'replied_to' => 'Hello'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->recipient->id,
            'sender_name' => $this->recipient->name,
            'recipient_id' => $this->sender->id,
            'recipient_name' => $this->sender->name,
            'message' => 'ello govna',
            'replied_to' => 'Hello'
        ]);

        $this->actingAs($this->sender);
        $response = $this->get('/api/inbox');
        $response->assertStatus(200);
        $content = $response->json();

        $this->assertSameSize($content['inbox'], $content['sent']);

    }

    public function test_if_user_can_delete_sent_and_received_message()
    {
        $this->actingAs($this->sender);

        $response = $this->post('/api/inbox',[
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);

        $response = $this->delete("/api/inbox/5",[
            'option' => 'sent'
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseHas('messages', [
            'sender_id' => null,
            'sender_name' => $this->sender->name,
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);

        $this->actingAs($this->recipient);
        $response = $this->delete("/api/inbox/5",[
            'option' => 'inbox'
        ]);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('messages', [
            'sender_id' => null,
            'sender_name' => $this->sender->name,
            'recipient_id' => null,
            'recipient_name' => $this->recipient->name,
            'message' => 'Hello'
        ]);

    }
    public function test_if_wrong_option_passed_to_delete()
    {
        $this->actingAs($this->sender);
        $response = $this->delete("/api/inbox/5",[
            'option' => 'random'
        ]);
        $response->assertStatus(422);
    }
}
