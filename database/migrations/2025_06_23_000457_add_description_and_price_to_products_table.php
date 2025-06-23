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
        // First, add new columns
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->decimal('price', 10, 2)->default(0)->after('description');
            $table->boolean('is_active')->default(true)->after('price');
        });

        // Update existing data for is_perishable
        \DB::table('products')
            ->where('is_perishable', '1')
            ->orWhere('is_perishable', 'true')
            ->orWhere('is_perishable', 'yes')
            ->update(['is_perishable' => 1]);

        \DB::table('products')
            ->where('is_perishable', '0')
            ->orWhere('is_perishable', 'false')
            ->orWhere('is_perishable', 'no')
            ->orWhereNull('is_perishable')
            ->update(['is_perishable' => 0]);

        // Then modify the column type
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->boolean('is_perishable')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['description', 'price', 'is_active']);
            // Revert column changes if needed
            $table->string('name')->nullable()->change();
            $table->string('is_perishable')->nullable()->change();
        });
    }
};
