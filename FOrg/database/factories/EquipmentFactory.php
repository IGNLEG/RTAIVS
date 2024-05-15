<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
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
        $image = UploadedFile::fake()->image('photo1.jpg');
        $path = $image->getRealPath();
        $doc = file_get_contents($path);
        $base64 = base64_encode($doc);
        $mime = $image->getClientMimeType();
        $img_name = $image->getFilename();
        return [
            'code' => Arr::random(['Sound', 'Video', 'Recording', 'Storage', 'Stage', 'Other']) . '-' . fake()->numberBetween(1,9) . fake()->numberBetween(1,9) . fake()->numberBetween(1,9),
            'name' => fake()->text(20),
            'type' => Arr::random(['Sound', 'Video', 'Recording', 'Storage', 'Stage', 'Other']),
            'amount_total' => $amount_total,
            'amount_available' => fake()->numberBetween(1,$amount_total),
            'unit_price' => fake()->numberBetween(50, 100000),
            'rent_price' => fake()->numberBetween(10, 100),
            'profit' => fake()->numberBetween(10, 300000),
            'problems' => fake()->text(30),
            'warehouse_id' => $warehouse->id,
            'img_name' => $img_name,
            'img_mime' => $mime,
            'img_base64' => $base64
        ];
    }
}
