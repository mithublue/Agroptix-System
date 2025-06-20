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

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('origin')->comment('Source address\',')->nullable();
            $table->string('destination')->comment('Destination address\',')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->decimal('co2_estimate', 8, 2, )->nullable();
            $table->string('departure_time')->nullable();
            $table->string('arrival_time')->nullable();

            $table->foreign('batch_id')
                  ->references('id')
                  ->on('batches')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
