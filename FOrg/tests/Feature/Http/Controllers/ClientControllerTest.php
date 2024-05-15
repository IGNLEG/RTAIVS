<?php


namespace Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

    }

    public function test_if_admin_can_create_client(): void
    {
        $this->actingAs($this->admin);
        $company = Company::factory()->create();
        $response = $this->post('/api/clients', [
            'name' => 'Vardas',
            'surname' => 'Pavarde',
            'company_id' => $company->id,
            'phone' => '+37066351889',
            'email' => 'some@gmail.com'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('clients', [
            'name' => 'Vardas',
            'surname' => 'Pavarde',
            'company_id' => $company->id,
            'phone' => '+37066351889',
            'email' => 'some@gmail.com'
        ]);
    }

    public function test_if_admin_can_delete_client()
    {
        $this->actingAs($this->admin);

        $client = Client::factory()->create();

        $response = $this->delete("/api/clients/$client->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
            'name' => $client->name,
            'surname' => $client->surname
        ]);
    }

    public function test_if_admin_can_update_client()
    {
        $this->actingAs($this->admin);

        $client = Client::factory()->create();
        $response = $this->put("/api/clients/$client->id", [
            'name' => 'Paltukas'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Paltukas'
        ]);
    }

    public function test_if_admin_can_get_client()
    {
        $this->actingAs($this->admin);

        $client = Client::factory()->create();
        $response = $this->get("/api/clients/$client->id");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $client->id,
            'name' => $client->name,
        ]);
    }

    public function test_if_admin_can_get_client_list()
    {
        $this->actingAs($this->admin);

        Client::factory(15)->create();

        $response = $this->get("/api/clients");

        $response->assertStatus(200);

        $response->assertJsonCount(15);
    }
}
