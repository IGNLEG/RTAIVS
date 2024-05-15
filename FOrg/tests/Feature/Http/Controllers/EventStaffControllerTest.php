<?php

namespace Http\Controllers;

use App\Models\Vehicle;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventStaffControllerTest extends TestCase
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

    public function test_if_user_gets_his_events_by_date()
    {
        $this->actingAs($this->organizer);
        $event = Event::factory()->create();

        $this->put("/api/events/$event->id",[
            'event_status' => 'Planning',
            'staff_id' => [$this->regular->id]
        ]);

        $this->actingAs($this->regular);
        $from = urlencode($event->start_date->format('Y-m-d H:i:s'));
        $to = urlencode($event->end_date->format('Y-m-d H:i:s'));
        $response = $this->get("/api/staff/events?from=$from&to=$to");

        $response->assertStatus(200);

        $response->assertJsonCount(1);
    }

    public function test_if_user_gets_event_employees()
    {
        $this->actingAs($this->organizer);
        $event = Event::factory()->create();

        $this->put("/api/events/$event->id",[
            'event_status' => 'Planning',
            'staff_id' => [$this->regular->id]
        ]);

        $response = $this->get("/api/event/staff?event_id=$event->id");

        $response->assertStatus(200);
        $response->assertJsonCount(1);

    }
}
