<?php

namespace Http\Controllers;

use App\Http\Controllers\TagController;
use App\Models\Tag;
use Database\Seeders\RolesAndPermissionsSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $warehouse_worker;
    private User $regular;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->warehouse_worker = User::factory()->create();
        $this->warehouse_worker->assignRole('warehouse_worker');
        $this->regular = User::factory()->create();
        $this->regular->assignRole('user');


    }

    public function test_if_admin_can_create_tag(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/api/tags', [
            'category' => 'Sound',
            'name' => 'Buchovke'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tags', [
            'category' => 'Sound',
            'name' => 'Buchovke'
        ]);
    }
    public function test_if_admin_can_delete_tag(){
        $this->actingAs($this->admin);

        $tag = Tag::factory()->create();

        $response = $this->delete("/api/tags/$tag->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tags',[
            'id' => $tag->id,
            'category' => $tag->category,
            'name' => $tag->name
        ]);
    }
    public function test_if_admin_can_update_tag(){
        $this->actingAs($this->admin);

        $tag = Tag::factory()->create();
        $response = $this->put("/api/tags/$tag->id", [
            'category' => 'Other',
            'name' => 'Dilatorius'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'category' => 'Other',
            'name' => 'Dilatorius'
        ]);
    }
    public function test_if_admin_can_get_tag(){
        $this->actingAs($this->admin);

        $tag = Tag::factory()->create();
        $response = $this->get("/api/tags/$tag->id");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $tag->id,
            'category' => $tag->category,
            'name' => $tag->name
        ]);
    }
    public function test_if_admin_can_get_tag_list(){
        $this->actingAs($this->admin);

        Tag::factory(15)->create();

        $response = $this->get("/api/tags");

        $response->assertStatus(200);

        $response->assertJsonCount(15);
    }

    public function test_if_warehouse_worker_can_create_tag(): void
    {
        $this->actingAs($this->warehouse_worker);

        $response = $this->post('/api/tags', [
            'category' => 'Sound',
            'name' => 'Buchovke'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tags', [
            'category' => 'Sound',
            'name' => 'Buchovke'
        ]);
    }
    public function test_if_warehouse_worker_can_delete_tag(){
        $this->actingAs($this->warehouse_worker);

        $tag = Tag::factory()->create();

        $response = $this->delete("/api/tags/$tag->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tags',[
            'id' => $tag->id,
            'category' => $tag->category,
            'name' => $tag->name
        ]);
    }
    public function test_if_warehouse_worker_can_update_tag(){
        $this->actingAs($this->warehouse_worker);

        $tag = Tag::factory()->create();
        $response = $this->put("/api/tags/$tag->id", [
            'category' => 'Other',
            'name' => 'Dilatorius'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'category' => 'Other',
            'name' => 'Dilatorius'
        ]);
    }
    public function test_if_warehouse_worker_can_get_tag(){
        $this->actingAs($this->warehouse_worker);

        $tag = Tag::factory()->create();
        $response = $this->get("/api/tags/$tag->id");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $tag->id,
            'category' => $tag->category,
            'name' => $tag->name
        ]);
    }
    public function test_if_warehouse_worker_can_get_tag_list(){
        $this->actingAs($this->warehouse_worker);

        Tag::factory(15)->create();

        $response = $this->get("/api/tags");

        $response->assertStatus(200);

        $response->assertJsonCount(15);
    }
    public function test_if_regular_user_can_not_get_tag_list(){
        $this->actingAs($this->regular);

        Tag::factory(15)->create();

        $response = $this->get("/api/tags");

        $response->assertStatus(403);

        $response->assertJsonCount(1); //info message
    }
}
