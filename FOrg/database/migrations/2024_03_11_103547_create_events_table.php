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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(\App\Models\Client::class, 'client_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('event_type', ['Private', 'Public', 'Rent Order']);
            $table->enum('event_subtype', ['Wedding', 'Birthday', 'Corporate', 'Concert', 'Town Celebration', 'Holiday Related', 'Other']);
            $table->string('description');
            $table->enum('event_status', ['Draft', 'Planning', 'In Progress', 'Complete']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
