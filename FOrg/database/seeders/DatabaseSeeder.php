<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Client;
use App\Models\Company;
use App\Models\Equipment;
use App\Models\Event;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'warehouse_worker']);
        Role::create(['name' => 'organizer']);
        Role::create(['name' => 'user']);
        Client::factory(5)->create();
        Equipment::factory(15)->create();
        Event::factory(8)->create();
        Vehicle::factory(8)->create();

        $admin = \App\Models\User::factory()->create([
             'name' => 'Administratorius',
             'email' => 'admin@example.com',
        ]);

        $admin->assignRole('admin');
    }
}
