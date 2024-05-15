<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\WarehouseController;

use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regular;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->regular = User::factory()->create();
        $this->regular->assignRole('user');
    }

    public function test_if_admin_can_create_warehouse(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/api/warehouses', [
            'short_name' => 'fake',
            'address' => 'fake_address'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('warehouses', [
            'short_name' => 'fake',
            'address' => 'fake_address'
        ]);
    }
    public function test_if_admin_can_delete_warehouse(){
        $this->actingAs($this->admin);

        $warehouse = Warehouse::factory()->create();

        $response = $this->delete("/api/warehouses/$warehouse->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('warehouses',[
            'id' => $warehouse->id,
            'short_name' => $warehouse->short_name,
            'address' => $warehouse->address
        ]);
    }
    public function test_if_admin_can_update_warehouse(){
        $this->actingAs($this->admin);

        $warehouse = Warehouse::factory()->create();
        $response = $this->put("/api/warehouses/$warehouse->id", [
            'short_name' => 'updated_fake',
            'address' => 'updated_fake_address'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'short_name' => 'updated_fake',
            'address' => 'updated_fake_address'
        ]);
    }
    public function test_if_admin_can_get_warehouse(){
        $this->actingAs($this->admin);

        $warehouse = Warehouse::factory()->create();
        $response = $this->get("/api/warehouses/$warehouse->id");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $warehouse->id,
            'short_name' => $warehouse->short_name,
            'address' => $warehouse->address
        ]);
    }
    public function test_if_admin_can_get_warehouse_list(){
        $this->actingAs($this->admin);

        $warehouse = Warehouse::factory(15)->create();

        $response = $this->get("/api/warehouses");

        $response->assertStatus(200);

        $response->assertJsonCount(15);
    }
    public function test_if_regular_user_can_not_create_warehouse(): void
    {
        $this->actingAs($this->regular);

        $response = $this->post('/api/warehouses', [
            'short_name' => 'unreal',
            'address' => 'fake'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('warehouses', [
            'short_name' => 'unreal',
            'address' => 'fake'
        ]);
    }
}
