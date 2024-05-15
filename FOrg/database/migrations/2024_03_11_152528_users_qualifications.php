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
        Schema::create('users_qualifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBiginteger('users_id')->unsigned();
            $table->unsignedBiginteger('qualifications_id')->unsigned();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('qualifications_id')->references('id')->on('qualifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_qualifications');
    }
};
