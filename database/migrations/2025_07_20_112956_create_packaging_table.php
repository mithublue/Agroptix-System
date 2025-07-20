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
        Schema::create('packaging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('cascade');
            $table->string('qr_code')->unique()->nullable();
            $table->string('package_type', 100)->nullable();
            $table->string('material_type', 100)->nullable();
            $table->decimal('unit_weight_packaging', 8, 3)->nullable()->comment('Weight of one packaging unit in grams');
            $table->decimal('total_product_weight', 10, 3)->nullable()->comment('Net weight of the product inside the packaging in grams');
            $table->decimal('total_package_weight', 10, 3)->nullable()->comment('Total weight including packaging in grams');
            $table->integer('quantity_of_units')->nullable()->comment('Number of individual product units');
            $table->dateTime('packaging_start_time')->nullable();
            $table->dateTime('packaging_end_time')->nullable();
            $table->string('packaging_location', 255)->nullable();
            $table->foreignId('packer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('rpc_unit_id')->nullable();
            $table->boolean('cleanliness_checklist')->nullable()->default(false);
            $table->decimal('co2_estimate', 8, 3)->nullable()->comment('Estimated CO2 footprint in kg CO2e');
            $table->timestamps();

            // Add indexes for frequently queried columns
            $table->index('qr_code');
            $table->index('package_type');
            $table->index('material_type');
            $table->index('packaging_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging');
    }
};
