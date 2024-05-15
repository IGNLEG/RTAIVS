<?php

namespace Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EventFileControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $regular;
    private User $organizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->regular = User::factory()->create();
        $this->regular->assignRole('user');
        $this->organizer = User::factory()->create();
        $this->organizer->assignRole('organizer');
    }

    public function test_if_organizer_can_add_files_to_event()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();

        $response = $this->post("/api/event/files",[
            'file' => UploadedFile::fake()->image('image1.jpg'),
            'name' => 'image1',
            'event_id' => $event->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('event_files',[
            'name' => 'image1.jpg',
            'event_id' => $event->id
        ]);
    }

    public function test_if_user_can_get_all_event_files()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();

        $response = $this->post("/api/event/files",[
            'file' => UploadedFile::fake()->image('image2.jpg'),
            'name' => 'image2',
            'event_id' => $event->id
        ]);

        $this->actingAs($this->regular);

        $response = $this->get("/api/event/$event->id/files");

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_if_user_can_get_one_event_file()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();

        $response = $this->post("/api/event/files",[
            'file' => UploadedFile::fake()->image('image3.jpg'),
            'name' => 'image3',
            'event_id' => $event->id
        ]);

        $this->actingAs($this->regular);

        $response = $this->get("/api/event/files/3");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'image3.jpg',
            'event_id' => $event->id
        ]);
    }

    public function test_if_organizer_can_delete_event_file()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();

        $response = $this->post("/api/event/files",[
            'file' => UploadedFile::fake()->image('image4.jpg'),
            'name' => 'image4',
            'event_id' => $event->id
        ]);

        $response = $this->delete("/api/event/files/4");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('event_files', [
            'name' => 'image4.jpg',
            'event_id' => $event->id
        ]);
    }
}
