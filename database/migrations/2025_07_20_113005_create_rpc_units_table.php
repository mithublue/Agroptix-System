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
        Schema::create('rpc_units', function (Blueprint $table) {
            $table->id();
            $table->string('rpc_identifier', 255)->nullable()->unique()->comment('Physical QR code or RFID tag on the RPC');
            $table->decimal('capacity_kg', 8, 2)->nullable()->comment('Maximum capacity in kilograms');
            $table->string('material_type', 100)->nullable();
            $table->date('initial_purchase_date')->nullable();
            $table->dateTime('last_washed_date')->nullable();
            $table->integer('total_wash_cycles')->nullable()->default(0);
            $table->integer('total_reuse_count')->nullable()->default(0);
            $table->string('current_location', 255)->nullable();
            $table->enum('status', ['available', 'in_use', 'damaged', 'in_repair', 'retired'])->nullable()->default('available');
            $table->timestamps();
            
            // Add indexes for frequently queried columns
            $table->index('rpc_identifier');
            $table->index('material_type');
            $table->index('status');
            $table->index('current_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpc_units');
    }
};
