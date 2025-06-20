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
        Schema::disableForeignKeyConstraints();

        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20, )->nullable();
            $table->string('gps_lat')->nullable();
            $table->string('gps_long')->nullable();
            $table->string('production_method', 20, )->nullable();
            $table->string('area')->nullable();
            $table->string('status', 50, )->default('pending');
            $table->foreignId('owner_id')->constrained('users,');
            $table->foreignId('user_as_owner_id');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
