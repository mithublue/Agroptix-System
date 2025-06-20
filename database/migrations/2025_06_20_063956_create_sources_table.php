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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20, )->nullable();
            $table->string('gps_lat')->nullable();
            $table->string('gps_long')->nullable();
            $table->enum('production_method', ["['Natural'",""]);
            $table->string('area')->nullable();
            $table->string('status', 50, )->default('pending');
            $table->foreignId('user_as_owner_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
