<?php

namespace Http\Controllers;

use App\Models\Vehicle;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsVehicleControllerTest extends TestCase
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

    public function test_if_user_can_get_event_vehicle_list_for_processing()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();
        $vehicle = Vehicle::factory(5)->create();
        $vehicleIds = [];

        foreach ($vehicle as $index => $veh) {
            $vehicleIds[] = [$veh->id, 1];
        }

        $response = $this->put("/api/events/$event->id", [
            'event_status' => 'Planning',
            'vehicles_id' => $vehicleIds,
        ]);
        $this->actingAs($this->regular);

        $response = $this->get("/api/events/$event->id/vehicles");
        $response->assertStatus(200);
        $content = $response->json();

        $this->assertTrue(sizeof($content['toTake']) == sizeof($vehicle));
        $this->assertTrue(sizeof($content['toReturn']) == 0);
        $this->assertTrue(sizeof($content['all_veh']) == sizeof($vehicle));
    }

    public function test_if_event_vehicle_is_updated_correctly_after_processing()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();
        $vehicle = Vehicle::factory(5)->create();
        $vehicleIds = [];
        $vehIds = [];

        foreach ($vehicle as $index => $veh) {
            $vehicleIds[] = [$veh->id, 1];
            $vehIds[] = $veh->id;
        }

        $this->put("/api/events/$event->id", [
            'event_status' => 'Planning',
            'vehicles_id' => $vehicleIds,
        ]);
        $this->actingAs($this->regular);

        $response = $this->put("/api/events/$event->id/vehicles",[
            'option' => 'take',
            'veh_ids' => $vehIds
        ]);
        $response->assertStatus(200);
        $response = $this->get("/api/events/$event->id/vehicles");
        $content = $response->json();

        $this->assertTrue(sizeof($content['toTake']) == 0);
        $this->assertTrue(sizeof($content['toReturn']) == sizeof($vehicle));
        $this->assertTrue(sizeof($content['all_veh']) == sizeof($vehicle));

        $response = $this->put("/api/events/$event->id/vehicles",[
            'option' => 'return',
            'veh_ids' => $vehIds
        ]);
        $response->assertStatus(200);
        $response = $this->get("/api/events/$event->id/vehicles");
        $content = $response->json();

        $this->assertTrue(sizeof($content['toTake']) == 0);
        $this->assertTrue(sizeof($content['toReturn']) == 0);
        $this->assertTrue(sizeof($content['all_veh']) == sizeof($vehicle));

    }
}
