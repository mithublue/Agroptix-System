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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('fuel_type')->nullable()->after('vehicle_type');
            $table->decimal('route_distance', 10, 2)->nullable()->after('fuel_type');
            $table->string('mode')->nullable()->after('route_distance');
            $table->decimal('temperature', 5, 2)->nullable()->after('mode')->comment('Temperature in Celsius');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'fuel_type',
                'route_distance',
                'mode',
                'temperature'
            ]);
        });
    }
};
