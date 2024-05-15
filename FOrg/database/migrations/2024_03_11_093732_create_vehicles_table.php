<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignIdFor(\App\Models\Warehouse::class, 'warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->string('license_plate');
            $table->enum('make', ['Mercedes', 'Renault', 'Opel/Vauxhall', 'Ford', 'Volkswagen']);
            $table->string('model');
            $table->integer('amount_total');
            $table->integer('amount_available');
            $table->enum('gearbox', ['Manual', 'Automatic']);
            $table->integer('mileage');
            $table->double('mpg');
            $table->double('price_per_km');
            $table->double('profit')->default(0);
            $table->string('problems');
            $table->date('insurance_until');
            $table->date('inspection_until');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
