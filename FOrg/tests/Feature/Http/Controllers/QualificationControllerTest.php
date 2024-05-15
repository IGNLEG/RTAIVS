<?php

namespace Http\Controllers;

use App\Http\Controllers\QualificationController;
use App\Models\Qualification;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualificationControllerTest extends TestCase
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

    public function test_if_admin_can_create_qualification(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/api/qualifications', [
            'name' => 'fake'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('qualifications', [
            'name' => 'fake'
        ]);
    }
    public function test_if_admin_can_delete_qualification(){
        $this->actingAs($this->admin);

        $qualification = Qualification::factory()->create();

        $response = $this->delete("/api/qualifications/$qualification->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('qualifications',[
            'id' => $qualification->id,
            'name' => $qualification->name,
        ]);
    }
    public function test_if_admin_can_update_qualification(){
        $this->actingAs($this->admin);

        $qualification = Qualification::factory()->create();
        $response = $this->put("/api/qualifications/$qualification->id", [
            'name' => 'fake'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('qualifications', [
            'id' => $qualification->id,
            'name' => 'fake'
        ]);
    }
    public function test_if_admin_can_get_qualification(){
        $this->actingAs($this->admin);

        $qualification = Qualification::factory()->create();
        $response = $this->get("/api/qualifications/$qualification->id");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $qualification->id,
            'name' => $qualification->name,
        ]);
    }
    public function test_if_admin_can_get_qualification_list(){
        $this->actingAs($this->admin);

        Qualification::factory(15)->create();

        $response = $this->get("/api/qualifications");

        $response->assertStatus(200);

        $response->assertJsonCount(15);
    }
    public function test_if_regular_user_can_not_create_qualification(): void
    {
        $this->actingAs($this->regular);

        $response = $this->post('/api/qualifications', [
            'short_name' => 'unreal',
            'address' => 'fake'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('qualifications', [
            'name' => 'fake'
        ]);
    }
}
