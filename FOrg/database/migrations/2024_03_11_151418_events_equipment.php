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
        Schema::create('events_equipment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBiginteger('events_id')->unsigned();
            $table->unsignedBiginteger('equipment_id')->unsigned();
            $table->integer('equipment_amount');
            $table->boolean('equipment_taken')->default(false);
            $table->string('taken_by')->default('none');
            $table->boolean('equipment_returned')->default(false);
            $table->string('returned_by')->default('none');
            $table->foreign('events_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_equipment');
    }
};
