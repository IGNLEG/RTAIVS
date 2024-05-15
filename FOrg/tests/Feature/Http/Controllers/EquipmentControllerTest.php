<?php

namespace Http\Controllers;

use App\Http\Controllers\EquipmentController;
use App\Models\Equipment;
use App\Models\Tag;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;
use function Symfony\Component\Translation\t;

class EquipmentControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $warehouse_worker;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->warehouse_worker = User::factory()->create();
        $this->warehouse_worker->assignRole('warehouse_worker');
    }

    public function test_if_warehouse_worker_can_create_equipment_with_image()
    {
        $this->actingAs($this->warehouse_worker);

        $warehouse = Warehouse::factory()->create();
        $response = $this->post('/api/equipment',[
            'code' => 'Video-001',
            'name' => 'PAR',
            'type' => 'Video',
            'amount_total' => 12,
            'unit_price' => 49.99,
            'rent_price' => 5.99,
            'profit' => 0,
            'problems' => 'nera',
            'warehouse_id' => $warehouse->id,
            'img_file' => UploadedFile::fake()->image('photo1.jpg'),
            'img_name' => 'photo1.jpg'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('equipment',[
            'code' => 'Video-001',
            'name' => 'PAR',
            'type' => 'Video',
            'amount_total' => 12,
            'unit_price' => 49.99,
            'rent_price' => 5.99,
            'profit' => 0,
            'problems' => 'nera',
            'img_name' => 'photo1.jpg'
        ]);
    }
    public function test_if_warehouse_worker_can_create_equipment_without_image()
    {
        $this->actingAs($this->warehouse_worker);

        $warehouse = Warehouse::factory()->create();
        $response = $this->post('/api/equipment',[
            'code' => 'Video-001',
            'name' => 'PAR',
            'type' => 'Video',
            'amount_total' => 12,
            'unit_price' => 49.99,
            'rent_price' => 5.99,
            'profit' => 0,
            'problems' => 'nera',
            'warehouse_id' => $warehouse->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('equipment',[
            'code' => 'Video-001',
            'name' => 'PAR',
            'type' => 'Video',
            'amount_total' => 12,
            'unit_price' => 49.99,
            'rent_price' => 5.99,
            'profit' => 0,
            'problems' => 'nera',
            'warehouse_id' => $warehouse->id

        ]);
    }

    public function test_if_tags_are_attached_to_equipment_after_creation()
    {
        $this->actingAs($this->warehouse_worker);
        $warehouse = Warehouse::factory()->create();
        $tags = Tag::factory(5)->create();
        $tagIds = [];

        foreach ($tags as $index => $tag)
        {
            $tagIds[] = $tag->id;
        }

        $response = $this->post('/api/equipment',[
            'code' => 'Video-001',
            'name' => 'PAR',
            'type' => 'Video',
            'amount_total' => 12,
            'unit_price' => 49.99,
            'rent_price' => 5.99,
            'profit' => 0,
            'problems' => 'nera',
            'warehouse_id' => $warehouse->id,
            'tag_ids' => json_encode($tagIds)
        ]);
        $content = $response->json();
        $response->assertStatus(201);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $content['id'],
            'tags_id' => $tagIds[0]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $content['id'],
            'tags_id' => $tagIds[1]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $content['id'],
            'tags_id' => $tagIds[2]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $content['id'],
            'tags_id' => $tagIds[3]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $content['id'],
            'tags_id' => $tagIds[4]
        ]);
    }
    public function test_if_warehouse_worker_can_get_equipment_list()
    {
        $this->actingAs($this->warehouse_worker);

        Equipment::factory(5)->create();

        $response = $this->get("/api/equipment");

        $response->assertStatus(200);

        $response->assertJsonCount(5);
    }

    public function test_if_warehouse_worker_can_get_equipment()
    {
        $this->actingAs($this->warehouse_worker);

        $eq = Equipment::factory()->create();

        $response = $this->get("/api/equipment/$eq->id");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $eq->id,
            'warehouse_id' => $eq->warehouse_id,
            'code' => $eq->code,
            'name' => $eq->name,
            'type' => $eq->type
        ]);
    }

    public function test_if_warehouse_worker_can_update_equipment_with_image()
    {
        $this->actingAs($this->warehouse_worker);
        $eq = Equipment::factory()->create();

        $response = $this->put("/api/equipment/$eq->id",[
            'code' => 'NEWCODE',
            'name' => 'NEWNAME',
            'img_file' => UploadedFile::fake()->image('newimage.jpg'),
            'img_name' => 'newimage.jpg'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment', [
            'id' => $eq->id,
            'code' => 'NEWCODE',
            'name' => 'NEWNAME',
            'img_name' => 'newimage.jpg'
        ]);
    }
    public function test_if_warehouse_worker_can_update_equipment_without_image()
    {
        $this->actingAs($this->warehouse_worker);
        $eq = Equipment::factory()->create();

        $response = $this->put("/api/equipment/$eq->id",[
            'code' => 'NEWCODE',
            'name' => 'NEWNAME',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment', [
            'id' => $eq->id,
            'code' => 'NEWCODE',
            'name' => 'NEWNAME',
        ]);
    }
    public function test_if_tags_are_synced_to_equipment_after_updating()
    {
        $this->actingAs($this->warehouse_worker);
        $eq = Equipment::factory()->create();
        $tags = Tag::factory(5)->create();
        $tagIds = [];

        foreach ($tags as $index => $tag)
        {
            $tagIds[] = $tag->id;
        }
        $response = $this->put("/api/equipment/$eq->id",[
            'code' => 'NEWCODE',
            'name' => 'NEWNAME',
            'tag_ids' => json_encode($tagIds)
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $eq->id,
            'tags_id' => $tagIds[0]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $eq->id,
            'tags_id' => $tagIds[1]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $eq->id,
            'tags_id' => $tagIds[2]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $eq->id,
            'tags_id' => $tagIds[3]
        ]);
        $this->assertDatabaseHas('equipment_tags',[
            'equipment_id' => $eq->id,
            'tags_id' => $tagIds[4]
        ]);
    }

    public function test_if_warehouse_worker_can_delete_equipment()
    {
        $this->actingAs($this->warehouse_worker);
        $eq = Equipment::factory()->create();

        $response = $this->delete("/api/equipment/$eq->id");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('equipment',[
            'id' => $eq->id,
            'name' => $eq->name,
            'code' => $eq->code,
            'problems' => $eq->problems,
            'img_name' => $eq->img_name
        ]);
    }

}
