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
        Schema::create('compliance_standards', function (Blueprint $table) {
            $table->id();
            $table->string('region'); // e.g., 'EU', 'US', 'GCC'
            $table->string('crop_type'); // e.g., 'Tomato', 'Lettuce'
            $table->string('parameter_name'); // e.g., 'pesticide_residue', 'ph_level', 'nitrate'
            $table->decimal('min_value', 10, 4)->nullable();
            $table->decimal('max_value', 10, 4)->nullable();
            $table->string('unit')->default('mg/kg');
            $table->string('critical_action')->default('warning'); // 'warning', 'reject_batch'
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for fast lookup during validation
            $table->index(['region', 'crop_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_standards');
    }
};
