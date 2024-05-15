<?php


namespace Http\Controllers;

use App\Models\Client;
use App\Models\Equipment;
use App\Models\Event;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $organizer;
    private User $regular;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->organizer = User::factory()->create();
        $this->organizer->assignRole('organizer');
        $this->regular = User::factory()->create();
        $this->regular->assignRole('user');
    }

    public function test_if_organizer_can_create_event()
    {
        $this->actingAs($this->organizer);
        $client = Client::factory()->create();
        $response = $this->post('/api/events', [
            'location' => 'kauno 2',
            'phone' => '37023040506',
            'client_id' => $client->id,
            'start_date' => '2024-05-07',
            'end_date' => '2024-05-08',
            'event_type' => 'Public',
            'event_subtype' => 'Concert',
            'description' => 'none',
            'event_status' => 'Draft'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('events', [
            'location' => 'kauno 2',
            'phone' => '37023040506',
            'client_id' => $client->id,
            'start_date' => '2024-05-07',
            'end_date' => '2024-05-08',
            'event_type' => 'Public',
            'event_subtype' => 'Concert',
            'description' => 'none',
            'event_status' => 'Draft'
        ]);
    }

    public function test_if_organizer_can_create_event_with_equipment_vehicles_staff()
    {
        $this->actingAs($this->organizer);
        $client = Client::factory()->create();
        $staff = User::factory(5)->create();
        $equipment = Equipment::factory(5)->create();
        $vehicles = Vehicle::factory(5)->create();
        $equipmentIds = [];
        $vehiclesIds = [];
        $staffIds = [];

        foreach ($staff as $index => $employee) {
            $staffIds[] = $employee->id;
        }
        foreach ($equipment as $index => $eq) {
            $equipmentIds[] = [$eq->id, 1];
        }
        foreach ($vehicles as $index => $veh) {
            $vehiclesIds[] = [$veh->id, 1];
        }

        $response = $this->post('/api/events', [
            'location' => 'kauno 2',
            'phone' => '37023040506',
            'client_id' => $client->id,
            'start_date' => '2024-05-07',
            'end_date' => '2024-05-08',
            'event_type' => 'Public',
            'event_subtype' => 'Concert',
            'description' => 'none',
            'event_status' => 'Draft',
            'equipment_id' => $equipmentIds,
            'vehicles_id' => $vehiclesIds,
            'staff_id' => $staffIds
        ]);

        $response->assertStatus(201);
        $content = $response->json();

        $this->assertDatabaseHas('events', [
            'location' => 'kauno 2',
            'phone' => '37023040506',
            'client_id' => $client->id,
            'start_date' => '2024-05-07',
            'end_date' => '2024-05-08',
            'event_type' => 'Public',
            'event_subtype' => 'Concert',
            'description' => 'none',
            'event_status' => 'Draft'
        ]);

        foreach ($staffIds as $index => $staff) {
            $this->assertDatabaseHas('events_users', [
                'events_id' => $content['id'],
                'users_id' => $staff
            ]);
        }
        foreach ($equipmentIds as $index => $eq) {
            $this->assertDatabaseHas('events_equipment', [
                'events_id' => $content['id'],
                'equipment_id' => $eq[0],
                'equipment_amount' => $eq[1]
            ]);
        }
        foreach ($vehiclesIds as $index => $veh) {
            $this->assertDatabaseHas('events_vehicles', [
                'events_id' => $content['id'],
                'vehicles_id' => $veh[0],
                'vehicle_amount' => $veh[1]
            ]);
        }
    }

    public function test_if_organizer_can_update_event_and_db_data_is_updated()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();
        $staff = User::factory(5)->create();
        $equipment = Equipment::factory(5)->create();
        $vehicles = Vehicle::factory(5)->create();

        $equipmentIds = [];
        $vehiclesIds = [];
        $staffIds = [];

        foreach ($staff as $index => $employee) {
            $staffIds[] = $employee->id;
        }
        foreach ($equipment as $index => $eq) {
            $equipmentIds[] = [$eq->id, 1, $eq->amount_available];
        }
        foreach ($vehicles as $index => $veh) {
            $vehiclesIds[] = [$veh->id, 1, $veh->amount_available];
        }

        $response = $this->put("/api/events/$event->id", [
            'location' => 'kauno 2',
            'phone' => '37023040506',
            'event_type' => 'Public',
            'event_subtype' => 'Concert',
            'description' => 'none',
            'event_status' => 'Planning',
            'equipment_id' => $equipmentIds,
            'vehicles_id' => $vehiclesIds,
            'staff_id' => $staffIds
        ]);
        $response->assertStatus(200);


        foreach ($staffIds as $index => $staff) {
            $this->assertDatabaseHas('events_users', [
                'events_id' => $event->id,
                'users_id' => $staff
            ]);
        }
        foreach ($equipmentIds as $index => $eq) {
            $this->assertDatabaseHas('events_equipment', [
                'events_id' => $event->id,
                'equipment_id' => $eq[0],
                'equipment_amount' => $eq[1]
            ]);
            $response = $this->get("/api/equipment/$eq[0]");
            $eqContent = $response->json();
            $this->assertTrue($eqContent[0]['amount_available'] == $eq[2] - 1);
        }
        foreach ($vehiclesIds as $index => $veh) {
            $this->assertDatabaseHas('events_vehicles', [
                'events_id' => $event->id,
                'vehicles_id' => $veh[0],
                'vehicle_amount' => $veh[1]
            ]);
            $response = $this->get("/api/vehicles/$veh[0]");
            $vehContent = $response->json();
            $this->assertTrue($vehContent['amount_available'] == $veh[2] - 1);
        }
        $response = $this->put("/api/events/$event->id", [
            'event_status' => 'Complete',
            'km_travelled' => 300
        ]);
        $response->assertStatus(200);

        foreach ($equipmentIds as $index => $eq) {
            $response = $this->get("/api/equipment/$eq[0]");
            $eqContent = $response->json();
            $this->assertTrue($eqContent[0]['amount_available'] == $eq[2]);
        }

        foreach ($vehiclesIds as $index => $veh) {
            $response = $this->get("/api/vehicles/$veh[0]");
            $vehContent = $response->json();
            $this->assertTrue($vehContent['amount_available'] == $veh[2]);
        }

    }

    public function test_if_user_can_get_event()
    {
        $this->actingAs($this->regular);
        $event = Event::factory()->create();

        $response = $this->get("/api/events/$event->id");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $event->id,
            'location' => $event->location,
            'phone' => $event->phone,
            'client_id' => $event->client_id,
            'event_type' => $event->event_type,
            'event_subtype' => $event->event_subtype
        ]);
    }

    public function test_if_organizer_can_get_event_list()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory(20)->create();
        $response = $this->get("/api/events");
        $content = $response->json()['data'];
        $response->assertStatus(200);
        $this->assertTrue(sizeof($content) == 15);

    }

    public function test_if_organizer_can_delete_event()
    {
        $this->actingAs($this->organizer);
        $event = Event::factory()->create();

        $response = $this->delete("/api/events/$event->id");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
            'location' => $event->location,
            'phone' => $event->phone,
            'client_id' => $event->client_id,
            'event_type' => $event->event_type,
            'event_subtype' => $event->event_subtype
        ]);

    }


}
