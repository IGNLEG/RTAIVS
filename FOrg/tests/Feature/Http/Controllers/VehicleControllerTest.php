<?php

namespace Http\Controllers;

use App\Http\Controllers\VehicleController;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $warehouse_worker;
    private User $regular;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->warehouse_worker = User::factory()->create();
        $this->warehouse_worker->assignRole('warehouse_worker');
        $this->regular = User::factory()->create();
        $this->regular->assignRole('user');
    }

    public function test_if_warehouse_worker_can_create_vehicle()
    {
        $this->actingAs($this->warehouse_worker);

        $warehouse = Warehouse::factory()->create();

        $response = $this->post('/api/vehicles', [
            'warehouse_id' => $warehouse->id,
            'make' => 'Mercedes',
            'license_plate' => 'AAA666',
            'model' => 'AMG',
            'amount_total' => 1,
            'amount_available' => 1,
            'gearbox' => 'Manual',
            'mileage' => 99523,
            'mpg' => 5.5,
            'price_per_km' => 0.25,
            'problems' => 'none',
            'insurance_until' => '2024-05-30',
            'inspection_until' => '2024-05-30',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vehicles', [
            'warehouse_id' => $warehouse->id,
            'license_plate' => 'AAA666',
            'make' => 'Mercedes',
            'model' => 'AMG',
            'amount_total' => 1,
            'amount_available' => 1,
            'gearbox' => 'Manual',
            'mileage' => 99523,
            'mpg' => 5.5,
            'price_per_km' => 0.25,
            'problems' => 'none',
            'insurance_until' => '2024-05-30',
            'inspection_until' => '2024-05-30',
        ]);
    }
    public function test_if_warehouse_worker_can_delete_vehicle()
    {
        $this->actingAs($this->warehouse_worker);

        $vehicle = Vehicle::factory()->create();

        $response = $this->delete("/api/vehicles/$vehicle->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('vehicles',[
            'id' => $vehicle->id,
            'warehouse_id' => $vehicle->warehouse_id,
            'license_plate' => $vehicle->license_plate,
            'make' => $vehicle->make,
            'model' => $vehicle->model,
            'amount_total' => $vehicle->amount_total,
            'amount_available' => $vehicle->amount_available,
            'gearbox' => $vehicle->gearbox,
            'mileage' => $vehicle->mileage,
            'mpg' => $vehicle->mpg,
            'price_per_km' => $vehicle->price_per_km,
            'problems' => $vehicle->problems,
            'insurance_until' => $vehicle->insurance_until,
            'inspection_until' => $vehicle->inspection_until
        ]);
    }
    public function test_if_warehouse_worker_can_update_vehicle()
    {
        $this->actingAs($this->warehouse_worker);

        $vehicle = Vehicle::factory()->create();
        $response = $this->put("/api/vehicles/$vehicle->id", [
            'make' => 'Ford',
            'model' => 'Focus',
            'mileage' => 5656565,
            'problems' => 'nevaziuoja'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'warehouse_id' => $vehicle->warehouse_id,
            'license_plate' => $vehicle->license_plate,
            'make' => 'Ford',
            'model' => 'Focus',
            'amount_total' => $vehicle->amount_total,
            'amount_available' => $vehicle->amount_available,
            'gearbox' => $vehicle->gearbox,
            'mileage' => 5656565,
            'mpg' => $vehicle->mpg,
            'price_per_km' => $vehicle->price_per_km,
            'problems' => 'nevaziuoja',
            'insurance_until' => $vehicle->insurance_until,
            'inspection_until' => $vehicle->inspection_until
        ]);
    }
    public function test_if_regular_user_can_get_vehicle()
    {
        $this->actingAs($this->warehouse_worker);

        $vehicle = Vehicle::factory()->create();
        $response = $this->get("/api/vehicles/$vehicle->id");
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $vehicle->id,
            'warehouse_id' => $vehicle->warehouse_id,
            'license_plate' => $vehicle->license_plate,
            'make' => $vehicle->make,
            'model' => $vehicle->model,
            'amount_total' => $vehicle->amount_total,
            'amount_available' => $vehicle->amount_available,
            'gearbox' => $vehicle->gearbox,
            'mileage' => $vehicle->mileage,
            'mpg' => $vehicle->mpg,
            'price_per_km' => $vehicle->price_per_km,
            'problems' => $vehicle->problems,
            'insurance_until' => $vehicle->insurance_until,
            'inspection_until' => $vehicle->inspection_until
        ]);
    }
    public function test_if_regular_user_can_get_vehicle_list()
    {
        $this->actingAs($this->warehouse_worker);

        Vehicle::factory(15)->create();

        $response = $this->get("/api/vehicles");

        $response->assertStatus(200);

        $response->assertJsonCount(15);
    }
}
