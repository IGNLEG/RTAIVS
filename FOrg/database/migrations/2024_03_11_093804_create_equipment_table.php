<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignIdFor(\App\Models\Warehouse::class, 'warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->string('code');
            $table->string('name');
            $table->enum('type', ['Sound', 'Video', 'Recording', 'Storage', 'Stage', 'Other']);
            $table->integer('amount_total');
            $table->integer('amount_available')->default(0);
            $table->double('unit_price');
            $table->double('rent_price');
            $table->double('profit')->default(0);
            $table->string('problems')->nullable();
            $table->string('img_name')->nullable();
            $table->text('img_mime')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE equipment ADD img_base64 MEDIUMBLOB");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
