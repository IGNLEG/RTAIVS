<?php

namespace Http\Controllers;

use App\Http\Controllers\CompanyController;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
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

    public function test_if_admin_can_create_company(): void
    {
        $this->actingAs($this->admin);
        $company = Company::factory()->create();
        $response = $this->post('/api/companies', [
            'name' => 'Saltara',
            'address' => 'niekur',
            'owner' => 'Petras Grazulis',
            'phone' => '+37066351889',
            'email' => 'some@gmail.com',
            'audio_subrent' => true,
            'video_subrent' => true,
            'vehicle_subrent' => false,
            'storage_subrent' => false,
            'stage_subrent' => true
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('companies', [
            'name' => 'Saltara',
            'address' => 'niekur',
            'owner' => 'Petras Grazulis',
            'phone' => '+37066351889',
            'email' => 'some@gmail.com',
            'audio_subrent' => true,
            'video_subrent' => true,
            'vehicle_subrent' => false,
            'storage_subrent' => false,
            'stage_subrent' => true
        ]);
    }

    public function test_if_admin_can_delete_company()
    {
        $this->actingAs($this->admin);

        $company = Company::factory()->create();

        $response = $this->delete("/api/companies/$company->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
            'name' => $company->name,
            'address' => $company->address,
            'owner' => $company->owner
        ]);
    }

    public function test_if_admin_can_update_company()
    {
        $this->actingAs($this->admin);

        $company = Company::factory()->create();
        $response = $this->put("/api/companies/$company->id", [
            'name' => 'Koldunine',
            'owner' => 'Uzkalnis',
            'audio_subrent' => false,
            'video_subrent' => false,
            'vehicle_subrent' => false,
            'storage_subrent' => false,
            'stage_subrent' => false
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Koldunine',
            'owner' => 'Uzkalnis',
            'audio_subrent' => false,
            'video_subrent' => false,
            'vehicle_subrent' => false,
            'storage_subrent' => false,
            'stage_subrent' => false
        ]);
    }

    public function test_if_user_can_get_company()
    {
        $this->actingAs($this->regular);

        $company = Company::factory()->create();
        $response = $this->get("/api/companies/$company->id");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $company->id,
            'name' => $company->name,
            'address' => $company->address,
            'owner' => $company->owner,
            'phone' => $company->phone,
            'email' => $company->email,
            'audio_subrent' => (int)$company->audio_subrent,
            'video_subrent' => (int)$company->video_subrent,
            'storage_subrent' => (int)$company->storage_subrent,
            'stage_subrent' => (int)$company->stage_subrent
        ]);
    }

    public function test_if_user_can_get_company_list()
    {
        $this->actingAs($this->regular);

        Company::factory(15)->create();

        $response = $this->get("/api/companies");

        $response->assertStatus(200);

        $response->assertJsonCount(15);
    }
}
