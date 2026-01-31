<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('current_location_lat', 10, 8)->nullable();
            $table->decimal('current_location_lng', 11, 8)->nullable();
            $table->timestamp('last_location_update')->nullable();
            $table->enum('tracking_status', ['pending', 'in_transit', 'delivered', 'delayed'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['current_location_lat', 'current_location_lng', 'last_location_update', 'tracking_status']);
        });
    }
};
