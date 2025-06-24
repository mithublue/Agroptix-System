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
        // First, ensure any existing data is in a format that can be converted to date
        \DB::statement("UPDATE batches SET harvest_time = NULL WHERE harvest_time = '' OR harvest_time = '0000-00-00'");
        
        Schema::table('batches', function (Blueprint $table) {
            // Change the column type to date
            $table->date('harvest_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to string if needed
        Schema::table('batches', function (Blueprint $table) {
            $table->string('harvest_time')->nullable()->change();
        });
    }
};
