<?php

namespace Http\Controllers;

use App\Models\Equipment;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventEquipmentControllerTest extends TestCase
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

    public function test_if_user_can_get_event_equipment_list_for_processing()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();
        $equipment = Equipment::factory(5)->create();
        $equipmentIds = [];

        foreach ($equipment as $index => $eq) {
            $equipmentIds[] = [$eq->id, 1];
        }

        $this->put("/api/events/$event->id", [
            'event_status' => 'Planning',
            'equipment_id' => $equipmentIds,
        ]);
        $this->actingAs($this->regular);

        $response = $this->get("/api/events/$event->id/equipment");
        $response->assertStatus(200);
        $content = $response->json();

        $this->assertTrue(sizeof($content['toTake']) == sizeof($equipment));
        $this->assertTrue(sizeof($content['toReturn']) == 0);
        $this->assertTrue(sizeof($content['all_eq']) == sizeof($equipment));
        $this->assertTrue(sizeof($content['for_invoice']) == sizeof($equipment));
    }

    public function test_if_event_equipment_is_updated_correctly_after_processing()
    {
        $this->actingAs($this->organizer);

        $event = Event::factory()->create();
        $equipment = Equipment::factory(5)->create();
        $equipmentIds = [];
        $eqIds = [];

        foreach ($equipment as $index => $eq) {
            $equipmentIds[] = [$eq->id, 1];
            $eqIds[] = $eq->id;
        }

        $this->put("/api/events/$event->id", [
            'event_status' => 'Planning',
            'equipment_id' => $equipmentIds,
        ]);
        $this->actingAs($this->regular);
        $response = $this->get("/api/events/$event->id/equipment");
        $content = $response->json();


        $response = $this->put("/api/events/$event->id/equipment",[
            'option' => 'take',
            'eq_ids' => $eqIds
        ]);

        $response->assertStatus(200);
        $response = $this->get("/api/events/$event->id/equipment");
        $content = $response->json();
        $this->assertTrue(sizeof($content['toTake']) == 0);
        $this->assertTrue(sizeof($content['toReturn']) == sizeof($equipment));
        $this->assertTrue(sizeof($content['all_eq']) == sizeof($equipment));
        $this->assertTrue(sizeof($content['for_invoice']) == sizeof($equipment));

        $response = $this->put("/api/events/$event->id/equipment",[
            'option' => 'return',
            'eq_ids' => $eqIds
        ]);


        $response = $this->get("/api/events/$event->id/equipment");
        $response->assertStatus(200);
        $response = $this->get("/api/events/$event->id/equipment");
        $content = $response->json();
        $this->assertTrue(sizeof($content['toTake']) == 0);
        $this->assertTrue(sizeof($content['toReturn']) == 0);
        $this->assertTrue(sizeof($content['all_eq']) == sizeof($equipment));
        $this->assertTrue(sizeof($content['for_invoice']) == sizeof($equipment));

    }
}
