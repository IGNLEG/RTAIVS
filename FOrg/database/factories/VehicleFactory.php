<?php

namespace Database\Factories;

use App\Http\Controllers\WarehouseController;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $warehouse = Warehouse::factory()->create();
        $amount_total = fake()->numberBetween(1,100);
        return [
            'warehouse_id' => $warehouse->id,
            'license_plate' => fake()->text(6),
            'make' => Arr::random(['Mercedes', 'Renault', 'Opel/Vauxhall', 'Ford', 'Volkswagen']),
            'model' => fake()->text(10),
            'amount_total' => 1,
            'amount_available' => 1,
            'gearbox' => Arr::random(['Manual', 'Automatic']),
            'mileage' => fake()->numberBetween(1000,999999),
            'mpg' => fake()->numberBetween(5, 10),
            'price_per_km' => fake()->numberBetween(1,2),
            'problems' => fake()->realText(100),
            'insurance_until' => fake()->date(),
            'inspection_until' => fake()->date(),
        ];
    }
}
